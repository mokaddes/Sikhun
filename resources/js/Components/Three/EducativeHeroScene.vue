<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import * as THREE from 'three';

const container = ref(null);
let animationFrameId = null;
let renderer = null;
let scene = null;
let camera = null;
let clock = null;

// Track mouse & scroll for responsive interactive tilt
let mouseX = 0;
let mouseY = 0;
let targetX = 0;
let targetY = 0;
let scrollY = 0;
let targetScrollY = 0;

function onScroll() {
    targetScrollY = window.scrollY || 0;
}

// Reusable animated groups
let mainRig = null;
let bookGroup = null;
let leftPages = null;
let rightPages = null;
let orbitGroup = null;
let sparkSphere = null;
let gradCap = null;
let floatingCrystals = [];

function initScene() {
    if (!container.value) return;

    const width = container.value.clientWidth || 480;
    const height = container.value.clientHeight || 420;

    // 1. Scene & Clock
    scene = new THREE.Scene();
    clock = new THREE.Clock();

    // 2. Camera
    camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
    camera.position.set(0, 1.2, 5.5);
    camera.lookAt(0, 0, 0);

    // 3. Renderer with responsible pixel ratio
    renderer = new THREE.WebGLRenderer({
        antialias: true,
        alpha: true,
        powerPreference: 'high-performance',
    });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    container.value.appendChild(renderer.domElement);

    // 4. Lighting: Theatrical, warm key + electric cyan rim
    const ambientLight = new THREE.AmbientLight(0x0f172a, 2.5);
    scene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xfff7ed, 3.2);
    keyLight.position.set(4, 5, 4);
    scene.add(keyLight);

    const cyanRimLight = new THREE.DirectionalLight(0x00f2fe, 3.0);
    cyanRimLight.position.set(-4, -2, -3);
    scene.add(cyanRimLight);

    const goldPointLight = new THREE.PointLight(0xf59e0b, 2.8, 10);
    goldPointLight.position.set(0, 1.5, 1);
    scene.add(goldPointLight);

    // 5. Main Rig Root
    mainRig = new THREE.Group();
    scene.add(mainRig);

    // ----------------------------------------------------
    // BUILD STYLIZED 3D OPEN KNOWLEDGE BOOK
    // ----------------------------------------------------
    bookGroup = new THREE.Group();
    bookGroup.position.set(0, -0.3, 0);
    bookGroup.rotation.x = 0.35; // Tilt toward camera for appeal
    mainRig.add(bookGroup);

    // Hardcover Spine (Central pivot)
    const spineGeo = new THREE.CylinderGeometry(0.08, 0.08, 1.6, 16, 1, false, 0, Math.PI);
    const spineMat = new THREE.MeshStandardMaterial({
        color: 0x0f172a,
        roughness: 0.3,
        metalness: 0.6,
    });
    const spine = new THREE.Mesh(spineGeo, spineMat);
    spine.rotation.z = Math.PI / 2;
    spine.rotation.y = Math.PI / 2;
    bookGroup.add(spine);

    // Covers
    const coverGeo = new THREE.BoxGeometry(1.05, 0.04, 1.55);
    const coverMat = new THREE.MeshStandardMaterial({
        color: 0x1e293b,
        roughness: 0.25,
        metalness: 0.4,
    });

    const leftCover = new THREE.Mesh(coverGeo, coverMat);
    leftCover.position.set(-0.55, -0.02, 0);
    leftCover.rotation.z = 0.22; // Open angle
    bookGroup.add(leftCover);

    const rightCover = new THREE.Mesh(coverGeo, coverMat);
    rightCover.position.set(0.55, -0.02, 0);
    rightCover.rotation.z = -0.22;
    bookGroup.add(rightCover);

    // Book Pages (Luminescent ivory with soft blue glow)
    const pageMat = new THREE.MeshStandardMaterial({
        color: 0xf8fafc,
        roughness: 0.4,
        emissive: 0x0284c7,
        emissiveIntensity: 0.08,
    });

    const pageBlockGeo = new THREE.BoxGeometry(0.96, 0.1, 1.45);

    leftPages = new THREE.Mesh(pageBlockGeo, pageMat);
    leftPages.position.set(-0.5, 0.04, 0);
    leftPages.rotation.z = 0.18;
    bookGroup.add(leftPages);

    rightPages = new THREE.Mesh(pageBlockGeo, pageMat);
    rightPages.position.set(0.5, 0.04, 0);
    rightPages.rotation.z = -0.18;
    bookGroup.add(rightPages);

    // Gold Trim on Page Edges
    const trimGeo = new THREE.BoxGeometry(0.04, 0.09, 1.46);
    const trimMat = new THREE.MeshStandardMaterial({
        color: 0xf59e0b,
        roughness: 0.2,
        metalness: 0.8,
    });
    const leftTrim = new THREE.Mesh(trimGeo, trimMat);
    leftTrim.position.set(-0.99, 0.12, 0);
    leftTrim.rotation.z = 0.18;
    bookGroup.add(leftTrim);

    const rightTrim = new THREE.Mesh(trimGeo, trimMat);
    rightTrim.position.set(0.99, 0.12, 0);
    rightTrim.rotation.z = -0.18;
    bookGroup.add(rightTrim);

    // Silk Ribbon Bookmark (Orange flame)
    const ribbonMat = new THREE.MeshStandardMaterial({
        color: 0xf97316,
        roughness: 0.4,
        metalness: 0.2,
    });
    const ribbonGeo = new THREE.BoxGeometry(0.08, 0.02, 1.1);
    const ribbon = new THREE.Mesh(ribbonGeo, ribbonMat);
    ribbon.position.set(0.05, 0.08, 0.35);
    ribbon.rotation.x = 0.1;
    bookGroup.add(ribbon);

    // ----------------------------------------------------
    // ORBITAL NEURAL RINGS (From Sikhun Brand Identity)
    // ----------------------------------------------------
    orbitGroup = new THREE.Group();
    orbitGroup.position.set(0, 0.35, 0);
    mainRig.add(orbitGroup);

    // Outer Primary Ring (Cyan glow)
    const ringGeo1 = new THREE.TorusGeometry(1.4, 0.022, 16, 80);
    const ringMat1 = new THREE.MeshStandardMaterial({
        color: 0x00f2fe,
        emissive: 0x00f2fe,
        emissiveIntensity: 0.6,
        roughness: 0.2,
        metalness: 0.8,
    });
    const ring1 = new THREE.Mesh(ringGeo1, ringMat1);
    ring1.rotation.x = Math.PI / 3.2;
    ring1.rotation.y = Math.PI / 6;
    orbitGroup.add(ring1);

    // Secondary Cross Ring (Purple/Indigo)
    const ringGeo2 = new THREE.TorusGeometry(1.65, 0.016, 16, 80);
    const ringMat2 = new THREE.MeshStandardMaterial({
        color: 0x818cf8,
        emissive: 0x6366f1,
        emissiveIntensity: 0.4,
        roughness: 0.3,
        metalness: 0.7,
    });
    const ring2 = new THREE.Mesh(ringGeo2, ringMat2);
    ring2.rotation.x = -Math.PI / 3.6;
    ring2.rotation.y = -Math.PI / 4;
    orbitGroup.add(ring2);

    // AI Spark Celestial Node (The glowing star in the Sikhun logo)
    const sparkGeo = new THREE.SphereGeometry(0.12, 24, 24);
    const sparkMat = new THREE.MeshStandardMaterial({
        color: 0xffedd5,
        emissive: 0xf59e0b,
        emissiveIntensity: 1.8,
        roughness: 0.1,
    });
    sparkSphere = new THREE.Mesh(sparkGeo, sparkMat);
    orbitGroup.add(sparkSphere);

    // ----------------------------------------------------
    // FLOATING GRADUATION CAP / ACADEMIC EMBLEM
    // ----------------------------------------------------
    gradCap = new THREE.Group();
    gradCap.position.set(0, 0.8, 0);
    gradCap.scale.set(0.65, 0.65, 0.65);
    mainRig.add(gradCap);

    // Cap Rhombus (Diamond top plate)
    const capTopGeo = new THREE.BoxGeometry(0.85, 0.035, 0.85);
    const capMat = new THREE.MeshStandardMaterial({
        color: 0x0f172a,
        roughness: 0.3,
        metalness: 0.3,
    });
    const capTop = new THREE.Mesh(capTopGeo, capMat);
    capTop.rotation.y = Math.PI / 4; // Diamond orientation
    gradCap.add(capTop);

    // Cap Skull Crown
    const skullGeo = new THREE.CylinderGeometry(0.26, 0.28, 0.2, 16);
    const skull = new THREE.Mesh(skullGeo, capMat);
    skull.position.set(0, -0.1, 0);
    gradCap.add(skull);

    // Golden Tassel
    const tasselMat = new THREE.MeshStandardMaterial({
        color: 0xfbbf24,
        emissive: 0xd97706,
        emissiveIntensity: 0.5,
        roughness: 0.2,
        metalness: 0.8,
    });
    const buttonGeo = new THREE.SphereGeometry(0.04, 12, 12);
    const button = new THREE.Mesh(buttonGeo, tasselMat);
    button.position.set(0, 0.03, 0);
    gradCap.add(button);

    const tasselStringGeo = new THREE.BoxGeometry(0.015, 0.015, 0.38);
    const tasselString = new THREE.Mesh(tasselStringGeo, tasselMat);
    tasselString.position.set(0.18, 0.02, 0.18);
    tasselString.rotation.y = Math.PI / 4;
    gradCap.add(tasselString);

    // ----------------------------------------------------
    // FLOATING KNOWLEDGE CRYSTALS (Subtle Secondary Action)
    // ----------------------------------------------------
    const crystalGeo = new THREE.OctahedronGeometry(0.09, 0);
    const crystalColors = [0x00f2fe, 0xf97316, 0xa855f7, 0x10b981, 0x38bdf8];

    for (let i = 0; i < 7; i++) {
        const cMat = new THREE.MeshStandardMaterial({
            color: crystalColors[i % crystalColors.length],
            emissive: crystalColors[i % crystalColors.length],
            emissiveIntensity: 0.8,
            roughness: 0.2,
            metalness: 0.9,
        });
        const crystal = new THREE.Mesh(crystalGeo, cMat);

        const angle = (i / 7) * Math.PI * 2;
        const radius = 1.8 + (i % 2) * 0.4;
        crystal.position.set(
            Math.cos(angle) * radius,
            -0.3 + (i % 3) * 0.5,
            Math.sin(angle) * radius
        );
        mainRig.add(crystal);
        floatingCrystals.push({
            mesh: crystal,
            baseY: crystal.position.y,
            speed: 0.8 + (i * 0.2),
            offset: i * 0.9,
        });
    }

    // Event listeners
    window.addEventListener('resize', onResize);
    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('scroll', onScroll, { passive: true });

    // Start Animation Loop
    animate();
}

function onMouseMove(event) {
    if (!container.value) return;
    const rect = container.value.getBoundingClientRect();
    const x = event.clientX - rect.left - (rect.width / 2);
    const y = event.clientY - rect.top - (rect.height / 2);

    targetX = (x / rect.width) * 0.6;
    targetY = -(y / rect.height) * 0.6;
}

function onResize() {
    if (!container.value || !renderer || !camera) return;
    const width = container.value.clientWidth;
    const height = container.value.clientHeight;
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
}

// Classic 12 Principles in 3D Animation:
// Breathing, Anticipation, Overlapping Action & Easing
function animate() {
    animationFrameId = requestAnimationFrame(animate);

    if (!clock || !renderer || !scene || !camera) return;

    // Delta clamping (prevents massive jumps when tab suspended)
    const delta = Math.min(clock.getDelta(), 0.05);
    const time = clock.getElapsedTime();

    // 1. Interactive Mouse & Scroll Parallax (Damped spring follow)
    mouseX += (targetX - mouseX) * 0.05;
    mouseY += (targetY - mouseY) * 0.05;
    scrollY += (targetScrollY - scrollY) * 0.06;

    if (mainRig) {
        // Rotates smoothly on mouse hover and scrolls with user journey
        mainRig.rotation.y = (time * 0.22) + (mouseX * 0.7) + (scrollY * 0.0012);
        mainRig.rotation.x = (mouseY * 0.4) + (Math.sin(time * 0.8) * 0.04) + Math.min(scrollY * 0.0004, 0.2);
        mainRig.position.y = (Math.sin(time * 1.4) * 0.1) - Math.min(scrollY * 0.0008, 0.5); // Natural floating breath + scroll sink
    }

    // 2. Book Breathing & Subtle Page Waves (Overlapping Secondary Action)
    if (bookGroup) {
        bookGroup.rotation.y = Math.sin(time * 0.6) * 0.08;
    }
    if (leftPages && rightPages) {
        leftPages.rotation.z = 0.18 + Math.sin(time * 2.2) * 0.02;
        rightPages.rotation.z = -0.18 - Math.sin(time * 2.2 + 0.4) * 0.02;
    }

    // 3. AI Spark Orbital Trajectory (Parametric elliptical path)
    if (sparkSphere) {
        const orbitSpeed = time * 1.8;
        const orbitRadius = 1.4;
        sparkSphere.position.set(
            Math.cos(orbitSpeed) * orbitRadius,
            Math.sin(orbitSpeed) * 0.6 + 0.1,
            Math.sin(orbitSpeed) * orbitRadius
        );
    }

    // 4. Floating Graduation Cap (Subtle lag/settle relative to book)
    if (gradCap) {
        gradCap.position.y = 0.85 + Math.sin(time * 1.6 + 0.8) * 0.08;
        gradCap.rotation.y = Math.sin(time * 0.9) * 0.2;
    }

    // 5. Floating Knowledge Crystals (Deliberate arced ascent & settle)
    floatingCrystals.forEach(item => {
        item.mesh.position.y = item.baseY + Math.sin(time * item.speed + item.offset) * 0.18;
        item.mesh.rotation.x += delta * 0.8;
        item.mesh.rotation.y += delta * 1.2;
    });

    renderer.render(scene, camera);
}

onMounted(() => {
    // Respect reduced-motion preferences
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!prefersReducedMotion) {
        initScene();
    }
});

onUnmounted(() => {
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
    }
    window.removeEventListener('resize', onResize);
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('scroll', onScroll);

    // Deep disposal of geometries & materials to prevent memory leaks
    if (scene) {
        scene.traverse((obj) => {
            if (obj.isMesh) {
                if (obj.geometry) obj.geometry.dispose();
                if (obj.material) {
                    if (Array.isArray(obj.material)) {
                        obj.material.forEach((m) => m.dispose());
                    } else {
                        obj.material.dispose();
                    }
                }
            }
        });
    }

    if (renderer) {
        renderer.dispose();
        if (renderer.domElement && renderer.domElement.parentNode) {
            renderer.domElement.parentNode.removeChild(renderer.domElement);
        }
    }
});
</script>

<template>
    <div
        ref="container"
        class="relative w-full h-[360px] sm:h-[420px] md:h-[460px] flex items-center justify-center pointer-events-auto select-none"
        aria-label="Interactive 3D Knowledge Universe"
    >
        <!-- Ambient glowing backdrop spotlight -->
        <div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center">
            <div class="w-64 h-64 sm:w-80 sm:h-80 rounded-full bg-gradient-to-tr from-[#00d2ff]/15 via-[#6366f1]/15 to-[#ff9e00]/15 blur-3xl opacity-60"></div>
        </div>

        <!-- Floating Interactive Badges / Tooltips around the 3D scene -->
        <div class="pointer-events-none absolute top-3 right-3 sm:top-6 sm:right-6 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-[#0f172a]/90 border border-slate-200/90 dark:border-cyan-400/30 backdrop-blur text-[11px] font-bold text-cyan-600 dark:text-cyan-300 shadow-md flex items-center gap-1.5 animate-bounce">
            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
            <span>AI বুক রিডার ও স্মার্ট চ্যাট</span>
        </div>

        <div class="pointer-events-none absolute bottom-5 left-3 sm:bottom-7 sm:left-6 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-[#0f172a]/90 border border-slate-200/90 dark:border-amber-400/30 backdrop-blur text-[11px] font-bold text-amber-600 dark:text-amber-300 shadow-md flex items-center gap-1.5">
            <span>🎯</span>
            <span class="font-bangla-num">২০,০০০+ সলভড প্রশ্নব্যাংক</span>
        </div>

        <div class="pointer-events-none absolute bottom-3 right-4 sm:bottom-5 sm:right-8 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-[#0f172a]/90 border border-slate-200/90 dark:border-indigo-400/30 backdrop-blur text-[11px] font-bold text-indigo-600 dark:text-indigo-300 shadow-md flex items-center gap-1.5">
            <span>⚡</span>
            <span>ভুল উত্তরের স্বয়ংক্রিয় খাতা</span>
        </div>
    </div>
</template>
