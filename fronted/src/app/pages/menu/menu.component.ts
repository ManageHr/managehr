import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../../services/auth.service'; // Asegúrate de que este servicio sea necesario y esté importado correctamente si lo usas en otro lugar
import { CommonModule } from '@angular/common';
import { RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-menu',
  standalone: true,
  imports: [CommonModule, RouterOutlet],
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.scss']
})
export class MenuComponent implements OnInit {

  isCollapsed = false;
  isSubmenuOpen = false; // Directorio
  isSubmenuVacantesOpen = false; // Vacantes

  usuario: any = {};

  constructor(private router: Router) {} // Si AuthService no se usa en este componente, puedes quitarlo del constructor

  ngOnInit(): void {
    const usuarioGuardado = localStorage.getItem('usuario');
    if (usuarioGuardado) {
      this.usuario = JSON.parse(usuarioGuardado);
      console.log('Usuario cargado:', this.usuario);
    } else {
      console.log('No hay usuario en localStorage');
      // Considera qué hacer si no hay usuario, quizás redirigir al login
    }
  }

  logout(): void {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    this.router.navigate(['/login']);
  }

  toggleMenu(): void {
    this.isCollapsed = !this.isCollapsed;
  }

  navigateTo(path: string): void {
    this.router.navigate([path]);
    // Opcional: Puedes cerrar los submenús al navegar a una nueva ruta
    // this.isSubmenuOpen = false;
    // this.isSubmenuVacantesOpen = false;
  }

  isActive(path: string): boolean {
    return this.router.url === path;
  }

  // Función para alternar el submenú de Directorio
  toggleSubmenu(): void {
    this.isSubmenuOpen = !this.isSubmenuOpen;
    // Opcional: Cierra otros submenús cuando abres este
    // this.isSubmenuVacantesOpen = false;
  }

  // Función para alternar el submenú de Vacantes - CORREGIDA
  toggleSubmenuVacantes(): void {
    this.isSubmenuVacantesOpen = !this.isSubmenuVacantesOpen;
    // Opcional: Cierra otros submenús cuando abres este
    // this.isSubmenuOpen = false;
  }
}