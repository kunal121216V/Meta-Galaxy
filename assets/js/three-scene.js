document.addEventListener('DOMContentLoaded', () => {
    const canvasHost = document.getElementById('threeCanvas');
    if (!canvasHost || typeof THREE === 'undefined') {
        return;
    }

    try {
        const scene = new THREE.Scene();
        scene.fog = new THREE.Fog(0x070b17, 8, 18);

        const camera = new THREE.PerspectiveCamera(35, 1, 0.1, 100);
        camera.position.set(0, 1.2, 6);

        const renderer = new THREE.WebGLRenderer({ canvas: canvasHost, alpha: true, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setSize(canvasHost.clientWidth || 500, canvasHost.clientHeight || 500, false);

        const ambient = new THREE.AmbientLight(0xffffff, 1.2);
        scene.add(ambient);

        const keyLight = new THREE.DirectionalLight(0x9de7ff, 1.4);
        keyLight.position.set(3, 4, 5);
        scene.add(keyLight);

        const rimLight = new THREE.PointLight(0x7c8cff, 2.2, 30);
        rimLight.position.set(-4, 2, 3);
        scene.add(rimLight);

        const baseGroup = new THREE.Group();
        scene.add(baseGroup);

        const pedestal = new THREE.Mesh(
            new THREE.CylinderGeometry(1.7, 2.1, 0.6, 48),
            new THREE.MeshStandardMaterial({ color: 0x172033, metalness: 0.7, roughness: 0.45 })
        );
        pedestal.position.y = -1.55;
        baseGroup.add(pedestal);

        const ring = new THREE.Mesh(
            new THREE.TorusGeometry(2.2, 0.07, 24, 100),
            new THREE.MeshStandardMaterial({ color: 0x9de7ff, emissive: 0x162634, metalness: 0.75, roughness: 0.25 })
        );
        ring.rotation.x = Math.PI / 2;
        ring.position.y = -1.1;
        baseGroup.add(ring);

        const geometry = new THREE.BoxGeometry(1.3, 1.8, 0.45);
        const material = new THREE.MeshPhysicalMaterial({
            color: 0x1a2338,
            roughness: 0.3,
            metalness: 0.8,
            transmission: 0.08,
            clearcoat: 0.7,
            emissive: 0x091521,
        });
        const productMesh = new THREE.Mesh(geometry, material);
        productMesh.position.y = 0.3;
        baseGroup.add(productMesh);

        const particleGeometry = new THREE.BufferGeometry();
        const particleCount = 180;
        const positions = new Float32Array(particleCount * 3);
        for (let i = 0; i < particleCount; i++) {
            positions[i * 3] = (Math.random() - 0.5) * 12;
            positions[i * 3 + 1] = (Math.random() - 0.5) * 10;
            positions[i * 3 + 2] = (Math.random() - 0.5) * 8;
        }
        particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        const particles = new THREE.Points(
            particleGeometry,
            new THREE.PointsMaterial({ color: 0x9de7ff, size: 0.04, transparent: true, opacity: 0.8 })
        );
        scene.add(particles);

        const clock = new THREE.Clock();
        const render = () => {
            const t = clock.getElapsedTime();
            productMesh.rotation.y = t * 0.7;
            productMesh.rotation.x = Math.sin(t * 0.9) * 0.18;
            baseGroup.rotation.y = t * 0.42;
            ring.rotation.z = t * 0.55;
            particles.rotation.y = t * 0.12;
            particles.rotation.x = t * 0.08;

            const pointerX = (window.innerWidth / 2 - (window.mouseX || window.innerWidth / 2)) / window.innerWidth;
            const pointerY = (window.innerHeight / 2 - (window.mouseY || window.innerHeight / 2)) / window.innerHeight;
            camera.position.x += (pointerX * 0.5 - camera.position.x) * 0.05;
            camera.position.y += (-pointerY * 0.5 - camera.position.y + 1.2) * 0.05;
            camera.lookAt(0, 0.3, 0);

            renderer.render(scene, camera);
            requestAnimationFrame(render);
        };

        const handlePointer = (event) => {
            window.mouseX = event.clientX;
            window.mouseY = event.clientY;
        };
        window.addEventListener('pointermove', handlePointer, { passive: true });
        window.addEventListener('resize', () => {
            const width = canvasHost.clientWidth || 500;
            const height = canvasHost.clientHeight || 500;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height, false);
        });

        render();
    } catch (error) {
        console.warn('3D scene failed to initialize:', error);
    }
});
