<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Figura 3D - SuperPupi</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background-color: #f5f5dc; /* Fondo amarillo claro */
        }
        #canvas3d {
            display: block;
        }
        #info {
            position: absolute;
            top: 20%; /* Ajustar para ponerlo más cerca de la parte superior */
            left: 50%; /* Centrado horizontal */
            transform: translateX(-50%); /* Alineación exacta */
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            font-size: 24px;
            font-family: Arial, sans-serif;
            z-index: 10;
            text-align: center;
            width: 60%; /* Ancho para texto largo */
        }
        .button {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 20;
            background-color: #0077ff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <a href="perfil_usuario.php" class="button">Regresar</a>
    <div id="info">Haz clic en una cara para obtener información</div>
    <canvas id="canvas3d"></canvas>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Configuración básica
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById('canvas3d') });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

        // Luz
        const light = new THREE.PointLight(0xffffff, 1, 100);
        light.position.set(10, 10, 10);
        scene.add(light);

        // Geometría (Cubo con caras numeradas y colores)
        const geometry = new THREE.BoxGeometry();
        const materials = [
            new THREE.MeshStandardMaterial({ color: 0xff0000, name: 'Cara 1 Roja' }),
            new THREE.MeshStandardMaterial({ color: 0x00ff00, name: 'Cara 2 Verde' }),
            new THREE.MeshStandardMaterial({ color: 0x0000ff, name: 'Cara 3 Azul' }),
            new THREE.MeshStandardMaterial({ color: 0xffff00, name: 'Cara 4 Amarilla' }),
            new THREE.MeshStandardMaterial({ color: 0xff00ff, name: 'Cara 5 Morada' }),
            new THREE.MeshStandardMaterial({ color: 0x00ffff, name: 'Cara 6 Cian' }),
        ];
        const cube = new THREE.Mesh(geometry, materials);
        scene.add(cube);

        // Posición de la cámara
        camera.position.z = 5;

        // Raycaster y mouse
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();

        // Detectar clic en las caras
        const infoDiv = document.getElementById('info');
        window.addEventListener('click', (event) => {
            // Calcular posición del mouse
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

            // Hacer raycasting
            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObject(cube);

            // Mostrar información de la cara seleccionada
            if (intersects.length > 0) {
                const faceIndex = intersects[0].face.materialIndex;
                const materialName = materials[faceIndex].name;
                infoDiv.textContent = `Esta es la ${materialName}`; // Información más clara
            }
        });

        // Animación (mantener rotación del cubo)
        function animate() {
            requestAnimationFrame(animate);
            cube.rotation.x += 0.01; // Rotación continua del cubo
            cube.rotation.y += 0.01;
            renderer.render(scene, camera);
        }
        animate();

        // Ajustar tamaño al cambiar ventana
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
