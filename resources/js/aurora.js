/**
 * Rakaca Aurora — JavaScript
 * Package: paparee/rakaca
 *
 * Includes:
 * - Theme detection & toggle (data-theme attribute)
 * - Mobile menu toggle
 * - Three.js aurora wave background (page-wide, smooth cursor interaction)
 */

(function () {
    'use strict';

    /* ------------------------------------------------------------------ */
    /* Theme Toggle                                                         */
    /* ------------------------------------------------------------------ */
    (function () {
        var root = document.documentElement;
        var btn = document.getElementById('themeToggle');
        if (!btn) return;

        btn.addEventListener('click', function () {
            var current = root.getAttribute('data-theme');
            root.setAttribute('data-theme', current === 'dark' ? 'light' : 'dark');
        });
    })();

    /* ------------------------------------------------------------------ */
    /* Mobile Menu                                                          */
    /* ------------------------------------------------------------------ */
    (function () {
        var menuBtn = document.getElementById('menuToggle');
        var menu = document.getElementById('mobileMenu');
        if (!menuBtn || !menu) return;

        menuBtn.addEventListener('click', function () {
            var isOpen = menu.classList.toggle('open');
            menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        menu.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                menu.classList.remove('open');
                menuBtn.setAttribute('aria-expanded', 'false');
            });
        });
    })();

    /* ------------------------------------------------------------------ */
    /* Three.js Aurora Wave Background                                      */
    /* Page-wide fullscreen quad, single draw call.                        */
    /* ------------------------------------------------------------------ */
    (function () {
        var canvas = document.getElementById('bg-canvas');
        if (!canvas || typeof THREE === 'undefined') return;

        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var renderer, scene, camera, mesh, material;
        var mouseX = 0, mouseY = 0;
        var smoothX = 0, smoothY = 0;
        var clock = new THREE.Clock();
        var running = true;
        var rafId = null;

        /* -- Shaders --------------------------------------------------- */

        var VERTEX_SHADER = [
            'varying vec2 vUv;',
            'void main() {',
            '  vUv = uv;',
            '  gl_Position = vec4(position.xy, 0.0, 1.0);',
            '}'
        ].join('\n');

        // A single fullscreen quad — the entire aurora is drawn here.
        // Continuous 3-lobe cosine colour blend: no seam, no fract() reset.
        // The cursor glow is blended into intensity, not drawn separately.
        var FRAGMENT_SHADER = [
            'precision mediump float;',
            'varying vec2 vUv;',
            'uniform float uTime;',
            'uniform float uAspect;',
            'uniform vec3 uColorA;',
            'uniform vec3 uColorB;',
            'uniform vec3 uColorC;',
            'uniform float uOpacity;',
            'uniform float uBrightness;',
            'uniform vec2 uMouse;',
            'void main() {',
            '  vec2 uv = vUv;',
            '  float t = uTime;',

            '  float w1 = sin(uv.x * 3.0 + t * 0.22 + uMouse.x * 1.1) * 0.09;',
            '  float w2 = sin(uv.x * 5.4 - t * 0.16 + 1.7) * 0.06;',
            '  float w3 = sin(uv.x * 8.6 + t * 0.30 + 3.1) * 0.04;',
            '  float centerLine1 = 0.64 + uMouse.y * 0.06 + w1 + w2 + w3;',
            '  float dist1 = abs(uv.y - centerLine1);',
            '  float thickness1 = 0.16 + 0.04 * sin(t * 0.15 + uv.x * 4.0);',
            '  float band1 = smoothstep(thickness1, 0.0, dist1);',

            '  float w4 = sin(uv.x * 4.2 - t * 0.19 + 0.6) * 0.08;',
            '  float w5 = sin(uv.x * 7.0 + t * 0.24 + 2.4) * 0.05;',
            '  float centerLine2 = 0.30 - uMouse.y * 0.04 + w4 + w5;',
            '  float dist2 = abs(uv.y - centerLine2);',
            '  float thickness2 = 0.12 + 0.03 * sin(t * 0.12 + uv.x * 3.0 + 2.0);',
            '  float band2 = smoothstep(thickness2, 0.0, dist2) * 0.7;',

            '  float streak1 = smoothstep(0.03, 0.0, abs(uv.y - (centerLine1 + sin(uv.x * 12.0 + t * 0.4 + 5.0) * 0.05 - 0.10))) * 0.5;',
            '  float streak2 = smoothstep(0.025, 0.0, abs(uv.y - (centerLine1 - sin(uv.x * 9.0 - t * 0.3 + 2.0) * 0.04 + 0.12))) * 0.35;',
            '  float streak3 = smoothstep(0.025, 0.0, abs(uv.y - (centerLine2 + sin(uv.x * 10.0 + t * 0.35 + 1.0) * 0.04 - 0.08))) * 0.3;',

            '  float intensity = band1 + band2 + streak1 + streak2 + streak3;',

            // Continuous 3-lobe cosine blend — smooth loop, no seam
            '  float phase = (uv.x * 0.5 + t * 0.025 + w2) * 6.28318530718;',
            '  float wA = max(0.0, cos(phase));',
            '  float wB = max(0.0, cos(phase - 2.09439510239));',
            '  float wC = max(0.0, cos(phase - 4.18879020479));',
            '  float wSum = wA + wB + wC + 0.0001;',
            '  vec3 col = (uColorA * wA + uColorB * wB + uColorC * wC) / wSum;',

            // Cursor glow: the aurora brightens where the pointer is
            '  vec2 mp = vec2(uMouse.x * 0.5 + 0.5, uMouse.y * -0.5 + 0.5);',
            '  vec2 d = uv - mp;',
            '  d.x *= uAspect;',
            '  float mouseDist = length(d);',
            '  float mouseGlow = smoothstep(0.42, 0.0, mouseDist) * 0.55;',
            '  intensity += mouseGlow;',
            '  col = mix(col, uColorC, mouseGlow * 0.35);',

            '  float edgeFade = smoothstep(0.0, 0.10, uv.x) * smoothstep(1.0, 0.90, uv.x);',
            '  float vertFade = smoothstep(0.0, 0.16, uv.y) * smoothstep(1.0, 0.40, uv.y);',
            '  float alpha = clamp(intensity, 0.0, 1.0) * edgeFade * vertFade * uOpacity;',
            '  gl_FragColor = vec4(col * intensity * uBrightness, alpha);',
            '}'
        ].join('\n');

        /* -- Helpers --------------------------------------------------- */

        function getComputedColor(varName) {
            var val = getComputedStyle(document.documentElement).getPropertyValue(varName).trim();
            return val || '#3B5BFF';
        }

        function currentOpacity() {
            var theme = document.documentElement.getAttribute('data-theme');
            // Softer in light mode so it doesn't wash out the bright background
            return theme === 'dark' ? 0.46 : 0.42;
        }

        function currentBrightness() {
            var theme = document.documentElement.getAttribute('data-theme');
            return theme === 'dark' ? 0.8 : 1.05;
        }

        /* -- Init ------------------------------------------------------ */

        function init() {
            var w = canvas.clientWidth;
            var h = canvas.clientHeight;

            scene = new THREE.Scene();
            camera = new THREE.Camera();

            try {
                renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
            } catch (e) {
                return;
            }

            renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.75));
            renderer.setSize(w, h, false);

            var geometry = new THREE.PlaneBufferGeometry(2, 2);
            material = new THREE.ShaderMaterial({
                uniforms: {
                    uTime:       { value: 0 },
                    uAspect:     { value: w / h },
                    uColorA:     { value: new THREE.Color(getComputedColor('--primary')) },
                    uColorB:     { value: new THREE.Color(getComputedColor('--primary-2')) },
                    uColorC:     { value: new THREE.Color(getComputedColor('--accent')) },
                    uOpacity:    { value: currentOpacity() },
                    uBrightness: { value: currentBrightness() },
                    uMouse:      { value: new THREE.Vector2(0, 0.15) }
                },
                vertexShader:   VERTEX_SHADER,
                fragmentShader: FRAGMENT_SHADER,
                transparent:    true,
                blending:       THREE.AdditiveBlending,
                depthWrite:     false,
                depthTest:      false
            });

            mesh = new THREE.Mesh(geometry, material);
            scene.add(mesh);
            renderer.render(scene, camera);

            if (!reduceMotion) {
                animate();
                document.addEventListener('visibilitychange', function () {
                    running = !document.hidden;
                    if (running && !rafId) animate();
                });
            }
        }

        /* -- Render loop ----------------------------------------------- */

        function animate() {
            if (!running) { rafId = null; return; }
            material.uniforms.uTime.value = clock.getElapsedTime();

            smoothX += (mouseX - smoothX) * 0.045;
            smoothY += (mouseY - smoothY) * 0.045;
            material.uniforms.uMouse.value.set(smoothX, smoothY);

            renderer.render(scene, camera);
            rafId = requestAnimationFrame(animate);
        }

        /* -- Resize ---------------------------------------------------- */

        function onResize() {
            var w = canvas.clientWidth;
            var h = canvas.clientHeight;
            if (!w || !h) return;
            renderer.setSize(w, h, false);
            material.uniforms.uAspect.value = w / h;
            if (reduceMotion) renderer.render(scene, camera);
        }

        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(onResize, 200);
        });

        /* -- Cursor interaction ---------------------------------------- */

        window.addEventListener('pointermove', function (e) {
            mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
            mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
        }, { passive: true });

        /* -- Sync colours on theme change ------------------------------ */

        var themeToggleBtn = document.getElementById('themeToggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function () {
                if (!material) return;
                setTimeout(function () {
                    material.uniforms.uColorA.value.set(getComputedColor('--primary'));
                    material.uniforms.uColorB.value.set(getComputedColor('--primary-2'));
                    material.uniforms.uColorC.value.set(getComputedColor('--accent'));
                    material.uniforms.uOpacity.value    = currentOpacity();
                    material.uniforms.uBrightness.value = currentBrightness();
                    if (reduceMotion) renderer.render(scene, camera);
                }, 50);
            });
        }

        init();
    })();

})();
