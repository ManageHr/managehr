// src/main.ts
import { bootstrapApplication } from '@angular/platform-browser';
import { AppComponent } from './app/app.component';
import { provideHttpClient } from '@angular/common/http'; // Importamos provideHttpClient
// assuming appRouting is something related to routing providers you custom defined
import { appRouting } from './app/app.routes'; // Esto parece ser tu configuración de rutas

bootstrapApplication(AppComponent, {
  providers: [
    provideHttpClient(), // <--- Correcto: Proporciona HttpClient aquí
    appRouting // Esto parece ser cómo manejas las rutas en tu proyecto.
              // La forma estándar sería provideRouter(routes) si 'routes' es tu array de rutas,
              // pero si 'appRouting' funciona para ti, no afecta la provisión de HttpClient.
  ]
}).catch(err => console.error(err));