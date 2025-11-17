<?php
session_start();

// Videos
$videoMobile = 'uploads/videos/202511161730 (1).mp4'; // 9:16
$videoDesktop = 'uploads/videos/202511161730.mp4'; // 16:9
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Tienda - Artículos de Segunda Mano</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    .video-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      overflow: hidden;
      background-color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      padding: 0;
    }

    .video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .login-button-container {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 10;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
    }

    .login-button-container.show {
      opacity: 1;
    }

    .login-button-container.hidden {
      display: none;
    }

    .login-button {
      display: inline-block;
      padding: 18px 50px;
      background: #1e40af;
      color: #ffffff;
      text-decoration: none;
      font-size: 1.3rem;
      font-weight: 700;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(30, 64, 175, 0.4);
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-family: 'Arial', sans-serif;
      white-space: nowrap;
    }

    .login-button:hover {
      background: #1e3a8a;
      transform: translateY(-3px);
      box-shadow: 0 12px 32px rgba(30, 64, 175, 0.6);
    }

    .login-button:active {
      transform: translateY(-1px);
      box-shadow: 0 6px 16px rgba(30, 64, 175, 0.4);
    }

    @media (max-width: 768px) {
      .login-button {
        padding: 14px 32px;
        font-size: 1rem;
        letter-spacing: 1px;
      }
    }
  </style>
</head>
<body>
  <div class="video-container">
    <!-- El video se cargará dinámicamente según el dispositivo -->
    <video 
      id="main-video"
      class="video"
      playsinline
      autoplay
      muted
    >
      Tu navegador no soporta el elemento de video.
    </video>

    <!-- Botón de iniciar sesión (oculto inicialmente) -->
    <div id="login-button-container" class="login-button-container hidden">
      <a href="login.php" class="login-button">
        Iniciar Sesión
      </a>
    </div>
  </div>

  <script>
    // Variables desde PHP
    const videoMobile = <?php echo json_encode($videoMobile); ?>;
    const videoDesktop = <?php echo json_encode($videoDesktop); ?>;
    
    // Determinar qué video cargar según el ancho de pantalla
    const isMobile = window.innerWidth <= 768;
    const videoToLoad = isMobile ? videoMobile : videoDesktop;
    
    // Cargar el video correcto
    const mainVideo = document.getElementById('main-video');
    const source = document.createElement('source');
    source.src = videoToLoad;
    source.type = 'video/mp4';
    mainVideo.appendChild(source);
    mainVideo.load();
    
    console.log('Dispositivo:', isMobile ? 'Móvil' : 'Desktop');
    console.log('Video cargado:', videoToLoad);

    // DOMContentLoaded
    document.addEventListener('DOMContentLoaded', () => {
      const loginButtonContainer = document.getElementById('login-button-container');

      if (mainVideo && loginButtonContainer) {
        // Evento cuando el video termina
        mainVideo.addEventListener('ended', () => {
          loginButtonContainer.classList.remove('hidden');
          setTimeout(() => {
            loginButtonContainer.classList.add('show');
          }, 50);
        });

        // Manejar cambio de tamaño de ventana para recargar si cambia de móvil a desktop o viceversa
        const initialIsMobile = window.innerWidth <= 768;
        let resizeTimer;
        
        window.addEventListener('resize', () => {
          clearTimeout(resizeTimer);
          resizeTimer = setTimeout(() => {
            const currentIsMobile = window.innerWidth <= 768;
            if (currentIsMobile !== initialIsMobile) {
              // Recargar la página para cargar el video correcto
              location.reload();
            }
          }, 250);
        });

        // Intentar reproducir el video (algunos navegadores requieren interacción del usuario)
        const playPromise = mainVideo.play();
        if (playPromise !== undefined) {
          playPromise.catch((error) => {
            console.log('Autoplay bloqueado:', error);
            // Mostrar un botón de reproducción si el autoplay está bloqueado
            const playButton = document.createElement('button');
            playButton.textContent = '▶ Reproducir';
            playButton.style.cssText = `
              position: absolute;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
              z-index: 20;
              padding: 20px 40px;
              font-size: 1.5rem;
              background: #1e40af;
              color: white;
              border: none;
              border-radius: 12px;
              cursor: pointer;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
              font-weight: 700;
            `;
            playButton.addEventListener('click', () => {
              mainVideo.play();
              playButton.remove();
            });
            document.querySelector('.video-container').appendChild(playButton);
          });
        }
      }
    });
  </script>
</body>
</html>