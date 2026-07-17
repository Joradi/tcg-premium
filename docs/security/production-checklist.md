# Checklist de seguridad para producción

Este documento reúne los controles que deben validarse antes de publicar TCG Premium en Internet.

## Aplicación Laravel

- [ ] Configurar `APP_ENV=production`.
- [ ] Configurar `APP_DEBUG=false`.
- [ ] Configurar `APP_URL` con el dominio HTTPS definitivo.
- [ ] Generar una `APP_KEY` exclusiva para producción.
- [ ] No copiar la clave utilizada en desarrollo.
- [ ] Ejecutar `php artisan config:cache`.
- [ ] Ejecutar `php artisan route:cache`.
- [ ] Ejecutar `php artisan view:cache`.
- [ ] Confirmar que las páginas de error no muestran stack traces.
- [ ] Ejecutar la suite completa antes de cada despliegue.

## HTTPS y proxies

- [ ] Instalar un certificado TLS válido.
- [ ] Redirigir todo el tráfico HTTP hacia HTTPS.
- [ ] Confirmar que HSTS aparece en respuestas HTTPS.
- [ ] Configurar los proxies de confianza según el hosting utilizado.
- [ ] No confiar indiscriminadamente en encabezados enviados por cualquier IP.
- [ ] Verificar que Laravel detecte correctamente `X-Forwarded-Proto`.

## Sesiones y cookies

- [ ] Configurar `SESSION_SECURE_COOKIE=true`.
- [ ] Configurar `SESSION_ENCRYPT=true`.
- [ ] Mantener `SESSION_HTTP_ONLY=true`.
- [ ] Mantener `SESSION_SAME_SITE=lax`, salvo que una integración requiera otro valor.
- [ ] Utilizar Redis o una base de datos privada para las sesiones.
- [ ] Definir una duración de sesión apropiada para producción.

## Base de datos

- [ ] No exponer MySQL directamente a Internet.
- [ ] Utilizar un usuario exclusivo para la aplicación.
- [ ] Aplicar privilegios mínimos al usuario de la aplicación.
- [ ] Usar una contraseña larga y exclusiva.
- [ ] Exigir conexiones cifradas cuando la infraestructura lo permita.
- [ ] Automatizar backups cifrados.
- [ ] Probar periódicamente la restauración de backups.

## Protección DDoS y abuso

- [ ] Utilizar Cloudflare, un WAF o protección perimetral equivalente.
- [ ] Ocultar la dirección IP real del servidor de origen.
- [ ] Permitir tráfico web al origen únicamente desde el proxy autorizado.
- [ ] Mantener rate limiting en login, registro, recuperación y checkout.
- [ ] Utilizar Redis para rate limiting cuando existan varios servidores.
- [ ] Configurar límites de tamaño de solicitudes y tiempos de espera.
- [ ] Crear alertas ante aumentos anormales de tráfico.

## Código y dependencias

- [ ] Ejecutar `composer audit`.
- [ ] Ejecutar `npm audit`.
- [ ] No desplegar con vulnerabilidades conocidas sin evaluación documentada.
- [ ] Ejecutar `npm ci` durante el despliegue.
- [ ] Ejecutar `npm run build`.
- [ ] Evitar SQL crudo construido con datos del usuario.
- [ ] Evitar salida Blade sin escapar mediante `{!! !!}`.
- [ ] Revisar cualquier futura carga de archivos.
- [ ] Mantener PHP, Laravel, Node y el sistema operativo actualizados.

## Secretos

- [ ] No versionar archivos `.env`.
- [ ] Almacenar secretos en variables de entorno o un gestor de secretos.
- [ ] Rotar credenciales después de cualquier exposición.
- [ ] No registrar contraseñas, tokens, datos de pago ni claves privadas.
- [ ] Separar credenciales de desarrollo, pruebas y producción.

## Monitoreo y respuesta

- [ ] Centralizar logs de aplicación y servidor.
- [ ] Configurar alertas para errores 500, 401, 403 y 429 anormales.
- [ ] Registrar operaciones administrativas sensibles.
- [ ] Monitorear uso de CPU, memoria, disco, red y base de datos.
- [ ] Definir responsables y procedimiento de respuesta ante incidentes.
- [ ] Documentar cómo bloquear tráfico, rotar secretos y restaurar servicios.

## Controles pendientes

- [ ] Definir una Content Security Policy compatible con Livewire, Alpine y Vite.
- [ ] Configurar infraestructura real de WAF y DDoS.
- [ ] Configurar proxies de confianza al conocer el proveedor de hosting.
- [ ] Incorporar monitoreo, backups y alertas del entorno real.
