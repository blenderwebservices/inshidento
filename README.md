# Inshidento

Plataforma de gestión de incidencias con IA para automatización de respuestas y clasificación.

## 🚀 Características

- **Gestión de Incidencias**: Creación, seguimiento y resolución de incidencias.
- **Automatización con IA**: Clasificación automática de incidencias y respuestas sugeridas.
- **Integración con OpenAI**: Uso de modelos GPT para análisis inteligente.
- **Interfaz Moderna**: Diseño limpio y profesional con Tailwind CSS.

## 🛠️ Requisitos Previos

- PHP >= 8.2
- Composer
- Node.js (para assets de frontend)
- Base de datos MySQL/MariaDB

## 📦 Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repositorio>
   cd inshidento
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   ```
   Edita el archivo `.env` con tus credenciales de base de datos y API key de OpenAI.

4. **Generar Key de Aplicación**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones**
   ```bash
   php artisan migrate
   ```

6. **Instalar dependencias de Frontend**
   ```bash
   npm install
   ```

7. **Compilar Assets**
   ```bash
   npm run dev
   ```
   O para producción:
   ```bash
   npm run build
   ```

8. **Iniciar Servidor de Desarrollo**
   ```bash
   php artisan serve
   ```

## 🔐 Autenticación

- **Email**: [EMAIL_ADDRESS]`
- **Password**: `password`

## 🤖 Configuración de IA

Asegúrate de tener configurada tu API Key de OpenAI en el archivo `.env`:

```env
OPENAI_API_KEY=tu_clave_api_aqui
```

## 📂 Estructura del Proyecto

- `app/`: Lógica de la aplicación (Modelos, Controladores, etc.)
- `app/Services/AIService.php`: Servicio para la integración con OpenAI.
- `database/migrations/`: Migraciones de base de datos.
- `resources/css/`: Estilos CSS (Tailwind).
- `resources/js/`: Scripts JavaScript (Vue).
- `routes/`: Definición de rutas de la aplicación.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, crea un branch para tus cambios y abre un Pull Request.

## 📄 Licencia

Este proyecto es de código cerrado. Todos los derechos reservados.
