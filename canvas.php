<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REVOO - Modelo de Negocio Canvas</title>
    <style>
        * {
            margin: 0;
            padding: 0.3rem;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f4c91;
            padding: 20px;
            min-height: 100vh;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .canvas-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: repeat(2, auto);
            gap: 15px;
            max-width: 1600px;
            margin: 0 auto;
        }

        .canvas-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .canvas-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.2);
        }

        .canvas-box h3 {
            color: #0f4c91;
            margin-bottom: 15px;
            font-size: 1.1em;
            border-bottom: 2px solid #0f4c91;
            padding-bottom: 8px;
        }

        .canvas-box ul {
            list-style: none;
            font-size: 0.9em;
            color: #333;
        }

        .canvas-box li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .canvas-box li:before {
            content: "•";
            color: #0f4c91;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        /* Grid positioning */
        .asociaciones { grid-column: 1; grid-row: 1; }
        .actividades { grid-column: 2; grid-row: 1; }
        .propuesta { grid-column: 3; grid-row: 1 / 3; }
        .relaciones { grid-column: 4; grid-row: 1; }
        .segmentos { grid-column: 5; grid-row: 1; }
        .recursos { grid-column: 1; grid-row: 2; }
        .canales { grid-column: 2; grid-row: 2; }
        .ingresos { grid-column: 4 / 6; grid-row: 2; }
        .costes { grid-column: 1 / 3; grid-row: 3; }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-content h3 {
            color: #667eea;
            font-size: 2em;
            margin-bottom: 25px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        .modal-content ul {
            list-style: none;
            font-size: 1.3em;
            color: #333;
        }

        .modal-content li {
            margin-bottom: 15px;
            padding-left: 30px;
            position: relative;
            line-height: 1.6;
        }

        .modal-content li:before {
            content: "•";
            color: #667eea;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 1.5em;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 2em;
            color: #999;
            cursor: pointer;
            transition: color 0.3s;
            background: none;
            border: none;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            color: #333;
        }

        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            font-size: 0.9em;
        }

        .btn-volver {
            display: inline-block;
            padding: 15px 40px;
            background: #ffffff;
            color: #0f4c91;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-volver:hover {
            background: #f0f0f0;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .btn-volver:active {
            transform: translateY(-1px);
        }

        @media (max-width: 1200px) {
            .canvas-container {
                grid-template-columns: repeat(3, 1fr);
            }
            .asociaciones { grid-column: 1; grid-row: 1; }
            .actividades { grid-column: 2; grid-row: 1; }
            .recursos { grid-column: 3; grid-row: 1; }
            .propuesta { grid-column: 1 / 4; grid-row: 2; }
            .relaciones { grid-column: 1; grid-row: 3; }
            .canales { grid-column: 2; grid-row: 3; }
            .segmentos { grid-column: 3; grid-row: 3; }
            .ingresos { grid-column: 1 / 4; grid-row: 4; }
            .costes { grid-column: 1 / 4; grid-row: 5; }
        }

        @media (max-width: 768px) {
            .canvas-container {
                grid-template-columns: 1fr;
            }
            .canvas-box {
                grid-column: 1 !important;
                grid-row: auto !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REVOO</h1>
        <p>Plataforma de Compra y Venta de Productos Nuevos y de Segunda Mano</p>
    </div>

    <div class="canvas-container">
        <div class="canvas-box asociaciones" onclick="openModal('asociaciones')">
            <h3>Asociaciones Clave</h3>
            <ul>
                <li>Emprendedores y artesanos locales</li>
                <li>Universidades (UIS y otras)</li>
                <li>Pasarelas de pago</li>
                <li>Entidades de apoyo al emprendimiento</li>
            </ul>
        </div>

        <div class="canvas-box actividades" onclick="openModal('actividades')">
            <h3>Actividades Clave</h3>
            <ul>
                <li>Desarrollo y mantenimiento de la plataforma</li>
                <li>Gestión de usuarios</li>
                <li>Publicidad y difusión digital</li>
            </ul>
        </div>

        <div class="canvas-box propuesta" onclick="openModal('propuesta')">
            <h3>Propuesta de Valor</h3>
            <ul>
                <li>Marketplace colombiano accesible</li>
                <li>Compra y venta segura, rápida y económica</li>
                <li>Fomento de la economía circular</li>
            </ul>
        </div>

        <div class="canvas-box relaciones" onclick="openModal('relaciones')">
            <h3>Relación con Clientes</h3>
            <ul>
                <li>Sistema de reputación y calificación</li>
                <li>Soporte al cliente</li>
            </ul>
        </div>

        <div class="canvas-box segmentos" onclick="openModal('segmentos')">
            <h3>Segmentos de Clientes</h3>
            <ul>
                <li>Estudiantes universitarios</li>
                <li>Emprendedores</li>
                <li>Compradores generales</li>
            </ul>
        </div>

        <div class="canvas-box recursos" onclick="openModal('recursos')">
            <h3>Recursos Clave</h3>
            <ul>
                <li>Servidores, hosting y dominio</li>
                <li>Comunidad activa de usuarios</li>
                <li>Relaciones con aliados comerciales</li>
            </ul>
        </div>

        <div class="canvas-box canales" onclick="openModal('canales')">
            <h3>Canales</h3>
            <ul>
                <li>Página web oficial</li>
                <li>Redes sociales (Facebook, Instagram, TikTok)</li>
            </ul>
        </div>

        <div class="canvas-box ingresos" onclick="openModal('ingresos')">
            <h3>Fuentes de Ingresos</h3>
            <ul>
                <li>Comisión del 5–10% por transacción</li>
                <li>Membresías premium para vendedores frecuentes</li>
            </ul>
        </div>

        <div class="canvas-box costes" onclick="openModal('costes')">
            <h3>Costes de Estructura</h3>
            <ul>
                <li>Mantenimiento y actualización del sitio</li>
                <li>Hosting, dominios y herramientas</li>
                <li>Publicidad y marketing digital</li>
                <li>Gastos legales y contables</li>
                <li>Soporte técnico</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <a href="inicio.php" class="btn-volver">Volver al Inicio</a>
    </div>

    <div class="modal" id="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">×</button>
            <div id="modal-body"></div>
        </div>
    </div>

    <script>
        const content = {
            asociaciones: {
                title: 'Asociaciones Clave',
                items: [
                    'Emprendedores y artesanos locales',
                    'Universidades (como la UIS y otras instituciones)',
                    'Pasarelas de pago (Nequi, Daviplata, PSE, Bancolombia)',
                    'Entidades de apoyo al emprendimiento juvenil'
                ]
            },
            actividades: {
                title: 'Actividades Clave',
                items: [
                    'Desarrollo y mantenimiento de la plataforma web/móvil',
                    'Gestión de usuarios (vendedores y compradores)',
                    'Publicidad y difusión digital (redes, universidades, comunidades)'
                ]
            },
            propuesta: {
                title: 'Propuesta de Valor',
                items: [
                    'Marketplace colombiano accesible para cualquier persona',
                    'Compra y venta segura, rápida y económica de artículos nuevos y de segunda mano',
                    'Fomento de la economía circular y el emprendimiento local'
                ]
            },
            relaciones: {
                title: 'Relación con Clientes',
                items: [
                    'Sistema de reputación y calificación',
                    'Soporte al cliente para disputas o reclamos'
                ]
            },
            segmentos: {
                title: 'Segmentos de Clientes',
                items: [
                    'Estudiantes universitarios (vendedores o compradores del mercadillo)',
                    'Emprendedores que ofrecen productos nuevos o artesanales',
                    'Compradores generales interesados en artículos nuevos o usados'
                ]
            },
            recursos: {
                title: 'Recursos Clave',
                items: [
                    'Servidores, hosting y dominio',
                    'Comunidad activa de usuarios',
                    'Relaciones con aliados comerciales'
                ]
            },
            canales: {
                title: 'Canales',
                items: [
                    'Página web oficial: https://revoo.modelosdenegocios.fun/',
                    'Redes sociales (Facebook, Instagram, TikTok)'
                ]
            },
            ingresos: {
                title: 'Fuentes de Ingresos',
                items: [
                    'Comisión del 5–10% por transacción',
                    'Membresías premium para vendedores frecuentes'
                ]
            },
            costes: {
                title: 'Costes de Estructura',
                items: [
                    'Mantenimiento y actualización del sitio web',
                    'Hosting, dominios y herramientas tecnológicas',
                    'Publicidad y marketing digital',
                    'Gastos legales, contables y de registro',
                    'Incentivos o pagos al equipo de trabajo',
                    'Soporte técnico y atención al usuario'
                ]
            }
        };

        function openModal(section) {
            const modal = document.getElementById('modal');
            const modalBody = document.getElementById('modal-body');
            const data = content[section];

            if (!modal || !modalBody || !data) return;

            modalBody.innerHTML = `
                <h3>${data.title}</h3>
                <ul>
                    ${data.items.map((item) => `<li>${item}</li>`).join('')}
                </ul>
            `;

            modal.classList.add('active');
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            if (!modal) return;
            modal.classList.remove('active');
        }

        // Close modal when clicking outside
        const modalElement = document.getElementById('modal');
        if (modalElement) {
            modalElement.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>