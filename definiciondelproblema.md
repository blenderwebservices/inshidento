Documento de Definición del Problema: Sistema Ecosistema de Incidencias

1. Contexto del Problema

En entornos operativos complejos (oficinas, plantas industriales, complejos residenciales), las fallas de infraestructura (eléctricas, plomería, mantenimiento, TI) suelen ser gestionadas de forma fragmentada. La falta de un canal centralizado de reporte y una trazabilidad clara provoca:

Tiempos de respuesta lentos: La información tarda en llegar al ejecutor adecuado.

Falta de evidencia: Los reportes carecen de detalles técnicos (fotos, audios, video), lo que obliga al técnico a realizar visitas de diagnóstico adicionales.

Desorden en la prioridad: No hay una jerarquía clara de qué incidencia es más crítica.

Opacidad en la resolución: El usuario que reporta no sabe el estado de su solicitud.

2. Declaración del Problema

Actualmente, no existe una herramienta integrada que conecte de manera eficiente a los Detectores (quienes ven el problema), los Gestores (quienes filtran y asignan) y los Fixers (quienes resuelven). Esto resulta en una degradación de la infraestructura y costos operativos elevados por falta de mantenimiento preventivo y correctivo oportuno.

3. Propuesta de Solución: Ecosistema Digital

Se propone el desarrollo de una plataforma multiplataforma compuesta por un Backend centralizado y tres interfaces móviles especializadas.

3.1. Arquitectura de Software

Backend: API REST robusta encargada de la persistencia de datos, lógica de negocio, notificaciones Push y almacenamiento de archivos multimedia.

App del Notificador (Reportero): Enfocada en la simplicidad y rapidez para reportar.

App del Gestor/Recolector: Panel de control para el triaje y la gestión de recursos.

App del Fixer (Ejecutor): Herramienta de trabajo de campo para la resolución y cierre de tickets.

4. Perfiles de Usuario y Funcionalidades de las Apps

A. App del Notificador (Detectores)

Su objetivo es capturar la incidencia en el momento exacto del hallazgo.

Captura Multimedia: Interfaz para tomar fotos, grabar audio (explicando la falla), video corto y redacción de texto.

Geolocalización/Origen: Selección automática o manual de la ubicación de la falla (ej. Piso 3, Oficina 202).

Seguimiento: Historial de mis reportes y notificaciones de cambio de estado (ej. "Tu reporte ya fue asignado").

B. App del Gestor (Recolectores y Administradores)

Es el "cerebro" que decide el destino de cada ticket.

Bandeja de Entrada: Visualización de todas las incidencias entrantes en tiempo real.

Triaje y Clasificación: Etiquetado por tipo (Eléctrica, TI, etc.) y nivel de prioridad.

Modelos de Asignación:

Asignación Directa: Seleccionar un técnico específico basado en su carga de trabajo o especialidad.

Publicación en Cola: Enviar la incidencia a una "bolsa de trabajo" común para que los Fixers la tomen.

Monitoreo: Vista de mapa o lista para ver dónde están los técnicos y qué incidencias están abiertas.

C. App del Fixer (Ejecutores)

Orientada a la productividad y la prueba de resolución.

Gestión de Tareas:

Aceptar tareas asignadas directamente.

Explorar la "Cola de Incidencias" y auto-asignarse una según su ubicación o especialidad.

Flujo de Trabajo: Botón de "Iniciar Trabajo", "En Pausa" (por falta de repuestos) y "Finalizar".

Evidencia de Cierre: Obligatoriedad de subir una foto del problema resuelto y una firma o comentario final.

5. Requerimientos de Datos (La Incidencia)

Para que el sistema sea efectivo, cada registro de incidencia debe contener:

ID Único: Código de rastreo.

Origen: Ubicación física o lógica.

Identidad: Quién reporta y quién resuelve.

Multimedia: Array de URLs a imágenes, audios y videos.

Timestamp: Fecha/hora de creación, asignación y resolución.

Estado: (Abierto, Asignado, En Proceso, Resuelto, Cancelado).

6. Flujos de Trabajo Propuestos (Workflow)

Paso

Acción

Actor

1

Detección de falla y carga de multimedia

Notificador

2

Notificación al Gestor de nueva incidencia

Sistema/Backend

3

Clasificación y validación de la falla

Gestor

4

Opción A: Asignación manual a Fixer específico

Gestor

4

Opción B: Publicación en lista de espera

Gestor

5

Aceptación de la tarea

Fixer

6

Ejecución y carga de evidencia de cierre

Fixer

7

Cierre de ticket y notificación de satisfacción

Sistema

7. Objetivos Técnicos Críticos

Sincronización Offline: Capacidad de capturar datos sin internet y subirlos al recuperar conexión.

Notificaciones en Tiempo Real: Uso de WebSockets o FCM para alertas inmediatas.

Escalabilidad: El API debe soportar el crecimiento de usuarios y el peso de los archivos multimedia.

8. Acceso Landing-Backend, Navegación y Niveles de Usuario / Impersonalización

8.1. Control de Acceso y Autenticación:
- Frontend / Landing Page activa como portal principal institucional y de solicitud de demos.
- Acceso al Backend (Dashboard) protegido por autenticación mediante contraseña (correo y credenciales).

8.2. Disposición del Menú de Navegación:
- Menú lateral izquierdo tradicional (Sidebar Navigation) con soporte para colapsarse/ocultarse dinámicamente en lugar del menú superior previamente sugerido.

8.3. Niveles de Usuario y Restricciones de Visualización/Operación:
- Admin (Super Admin): Acceso total sin restricciones a todas las operaciones, configuración de empresas/tenants, gestión de sucursales, usuarios, incidentes y reportes de facturación.
- Gestor / Supervisor: Gestión y triaje de incidentes, asignación a fixers y monitoreo operativo.
- Fixer / Técnico: Visualización de cola de trabajo, auto-asignación, cambio de estado de trabajos y evidencia de cierre.
- Notificador / Reportero: Creación de tickets de incidencias y seguimiento de sus propios reportes.
- Administrador de Facturación: Acceso a reportes financieros de fixers externos y liquidación de servicios.

8.4. Función Especial de Impersonalización (Super Admin):
- El Super Admin cuenta con la capacidad de "Impersonalizar" (asumir temporalmente el rol y contexto de cualquier otro usuario/rol del sistema).
- Durante la impersonalización, se aplican estrictamente todas las restricciones visuales (menús, recursos, acciones) y funcionales del rol asumido, permitiendo auditar y confirmar la experiencia exacta de cada usuario.
- Se despliega una barra/notificación indicadora permanente de "Modo Impersonalizado" con un botón de retorno rápido para "Regresar a Super Admin".