Especificación Técnica: Ecosistema de Gestión de Incidencias

Este documento detalla la estructura de datos y los puntos de acceso de la API necesarios para soportar las tres aplicaciones móviles y el flujo de trabajo de gestión de fallas.

1. Modelo de Base de Datos (Relacional)

Se recomienda una base de datos SQL (PostgreSQL o MySQL) para mantener la integridad referencial de los estados y usuarios.

Tabla: users

Almacena a todos los participantes del sistema.

id (UUID, PK)

nombre (VARCHAR)

email (VARCHAR, UNIQUE)

password_hash (TEXT)

rol (ENUM: 'notifier', 'manager', 'fixer', 'admin')

especialidad (VARCHAR, NULL) - Solo para fixers (ej. Electricista)

fcm_token (TEXT) - Para notificaciones push

Tabla: categories

id (INT, PK)

nombre (VARCHAR) - (Ej: Eléctrica, Plomería, TI, Mantenimiento)

Tabla: incidents

La entidad central del sistema.

id (UUID, PK)

titulo (VARCHAR)

descripcion (TEXT)

categoria_id (FK -> categories)

prioridad (ENUM: 'baja', 'media', 'alta', 'critica')

estado (ENUM: 'abierta', 'asignada', 'en_progreso', 'resuelta', 'cancelada')

ubicacion_texto (VARCHAR) - Ej: Edificio A, Piso 2

latitud (DECIMAL) / longitud (DECIMAL)

notifier_id (FK -> users)

manager_id (FK -> users, NULL) - Quién la gestionó

fixer_id (FK -> users, NULL) - Quién la resuelve

fecha_creacion (TIMESTAMP)

fecha_resolucion (TIMESTAMP, NULL)

Tabla: incident_media

id (UUID, PK)

incident_id (FK -> incidents)

url_archivo (TEXT) - URL de S3 o Cloudinary

tipo (ENUM: 'image', 'audio', 'video')

Tabla: incident_logs

Historial para auditoría y trazabilidad.

id (UUID, PK)

incident_id (FK -> incidents)

estado_anterior (VARCHAR)

estado_nuevo (VARCHAR)

usuario_id (FK -> users)

comentario (TEXT)

fecha (TIMESTAMP)

2. Definición de la API REST

La API debe ser protegida mediante JWT (JSON Web Tokens).

Autenticación

POST /api/v1/auth/login: Retorna el token y el perfil/rol del usuario.

POST /api/v1/auth/register: (Opcional) Registro de nuevos notificadores.

Endpoints para el Notificador (App 1)

POST /api/v1/incidents: Crea una nueva incidencia (título, ubicación, descripción).

POST /api/v1/incidents/{id}/media: Sube archivos multimedia asociados.

GET /api/v1/my-reports: Lista de incidencias reportadas por el usuario actual.

Endpoints para el Gestor (App 2)

GET /api/v1/incidents/pending: Lista todas las incidencias con estado 'abierta'.

PATCH /api/v1/incidents/{id}/assign: Asigna un fixer_id y cambia el estado a 'asignada'.

PATCH /api/v1/incidents/{id}/queue: Clasifica y pone la incidencia en la "cola pública" para fixers.

GET /api/v1/fixers/available: Lista de técnicos activos y su carga de trabajo.

Endpoints para el Fixer (App 3)

GET /api/v1/incidents/queue: Lista de incidencias en cola esperando ser tomadas.

GET /api/v1/incidents/my-tasks: Incidencias asignadas al fixer logueado.

PATCH /api/v1/incidents/{id}/claim: El fixer toma una incidencia de la cola pública.

PATCH /api/v1/incidents/{id}/status: Actualiza a 'en_progreso' o 'resuelta'. Requiere adjuntar comentario y evidencia de cierre.

3. Lógica de "Cola vs Asignación"

Para implementar ambas opciones que mencionaste:

Asignación Directa: El Gestor usa el endpoint /assign. El sistema envía una notificación Push inmediata al Fixer seleccionado: "Se te ha asignado una nueva tarea: [Título]".

Cola de Trabajo: El Gestor usa el endpoint /queue. Todos los Fixers de esa categoría reciben una notificación: "Nueva incidencia disponible en tu zona". El primero en hacer click en el botón "Tomar Incidencia" (/claim) gana el ticket y este desaparece de la lista de los demás.

4. Consideraciones Multimedia

Dado que planeas usar audio y video:

Subida: Se recomienda que la App suba los archivos directamente a un bucket (AWS S3 / Google Cloud Storage) y envíe solo la URL a la API para no saturar el servidor de aplicaciones.

Formatos: Forzar formatos comprimidos (HEIC/JPG para fotos, AAC para audio, MP4 para video) para ahorrar ancho de banda.