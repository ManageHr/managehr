<p align="center">
  <img src="./fronted/src/assets/logoMHR.png" alt="Logo del Proyecto" width="200"/>
</p>

# ManageHR
## Integrantes
- Juan David Joven
- Sharon Nicolle López
- Oscar Andrés Muñoz
- Yon Piter Ruiz


**Manage HR** es un software integral para optimizar la gestión de recursos humanos en las empresas. Ofrece una interfaz intuitiva que facilita el manejo de datos, agiliza la selección y contratación de personal, incluye chat interno para mejorar la comunicación y permite gestionar permisos y horas extra de forma sencilla, mejorando así los procesos internos.


## 🎯 Objetivos``

✔ Diseñar una interfaz gráfica amigable e intuitiva para el usuario.  
✔ Controlar eficientemente las vacaciones, permisos y ausencias del personal.  
✔ Automatizar los procesos de reclutamiento y selección de talento humano.  
✔ Almacenar y administrar información detallada de cada empleado.  
✔ Garantizar la seguridad y confidencialidad de los datos de los usuarios.  
✔ Gestionar la emisión y control de paz y salvos.  
✔ Desarrollar el software **Manage HR** como herramienta para mejorar y optimizar los procesos del área de recursos humanos en las empresas.



## 📌 Alcance
Manage HR es una solución integral diseñada para cubrir un amplio espectro de funciones en la gestión de recursos humanos. El sistema permite organizar eficientemente los datos básicos de los empleados, automatizar procesos de reclutamiento y facilitar la comunicación interna mediante un chat integrado. Incorpora formularios intuitivos para gestionar solicitudes de permisos, horas extras e incapacidades, optimizando así la administración de ausencias y reduciendo errores operativos. Además, ofrece acceso ágil a hojas de vida y vacantes disponibles, promoviendo la movilidad interna y el desarrollo profesional de los colaboradores. En conjunto, estas funcionalidades convierten a Manage HR en una herramienta esencial para mejorar la eficiencia, la colaboración y la productividad en cualquier empresa

## 🚀 Tecnologías

- PHP 8.2
- MySQL
- Laravel 12
- Angular 18

## 📦 Instalación

### 🔧 **Backend (Laravel + PHP)**
```bash
instalar composer
instalar node js
dejarlas como predeterminadas en las variables de entorno

git clone https://github.com/ManageHr/managehr.git
cd managehr

php composer install
php artisan serve

Para seguridad si salen errores en angular
símbolo del sistema como administrador y pegar
Set-ExecutionPolicy RemoteSigned


### 💻 **Frontend (Angular)**

Angular npm install

Si sale error de ng no se reconoce

npm list -g @angular/cli
si no deja utilizar este
npm install -g @angular/cli


Angular ng serve

Para permisos de fotos en laravel
php artisan storage:link

Para reportes angular

npm install chart.js jspdf xlsx file-saver

Para fpdf

npm install jspdf jspdf-autotable
npm install --save-dev @types/jspdf-autotable

Para permitir descargas

npm install --save file-saver
npm install --save-dev @types/file-saver


Por si tiene errores
npm install --save-dev @types/file-saver @types/jspdf @types/xlsx


php documentacion

composer require darkaonline/l5-swagger
