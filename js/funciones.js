console.log("JS cargado correctamente");

document.addEventListener('DOMContentLoaded', () => {
	// Evitar doble envío al agregar al carrito
	const forms = document.querySelectorAll('form[action="php/agregar_carrito.php"]');
	forms.forEach(form => {
		form.addEventListener('submit', () => {
			const btn = form.querySelector('button[type="submit"]');
			if (btn && !btn.disabled) {
				btn.disabled = true;
				btn.classList.add('deshabilitado');
				const original = btn.getAttribute('data-original-text') || btn.textContent;
				btn.setAttribute('data-original-text', original);
				btn.textContent = 'Agregando...';
				// Rehabilitar por si hay navegación cancelada (fallback)
				setTimeout(() => {
					if (btn) {
						btn.disabled = false;
						btn.classList.remove('deshabilitado');
						btn.textContent = original;
					}
				}, 4000);
			}
		}, { once: true });
	});
});
