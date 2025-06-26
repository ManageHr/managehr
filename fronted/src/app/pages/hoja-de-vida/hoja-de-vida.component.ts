import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MenuComponent } from '../menu/menu.component';

@Component({
  selector: 'app-hoja-de-vida',
  standalone: true,
  templateUrl: './hoja-de-vida.component.html',
  styleUrls: ['./hoja-de-vida.component.scss'],
  imports: [CommonModule, FormsModule, MenuComponent]
})
export class HojaDeVidaComponent implements OnInit {
  // Datos de hoja de vida
  hojaDeVida = {
    claseLibretaMilitar: 'Primera',
    numeroLibretaMilitar: 'LM-98765'
  };

  // Arrays iniciales
  estudios: any[] = [
    {
      nomEstudio: 'Ingeniería de Sistemas',
      nomInstitucion: 'Universidad Nacional',
      tituloObtenido: 'Ingeniera',
      anioInicio: '2018-01-01',
      anioFinalizacion: '2022-12-01',
      abierto: false
    }
  ];

  experiencias: any[] = [
    {
      nomEmpresa: 'Tech Solutions',
      nomJefe: 'Carlos Pérez',
      telefono: '3001234567',
      cargo: 'Desarrolladora Full Stack',
      actividades: 'Desarrollo de aplicaciones web en Angular y Laravel.',
      fechaInicio: '2023-01-15',
      fechaFinalizacion: '2024-05-30',
      abierto: false
    }
  ];

  // Variables de modal
  mostrarModalEditarLibreta = false;
  mostrarModalAgregarEstudio = false;
  mostrarModalAgregarExperiencia = false;

  // Nuevos datos a agregar
  nuevoEstudio: any = {};
  nuevaExperiencia: any = {};

  constructor() {}

  ngOnInit(): void {}

  // Libreta Militar
  abrirModalEditarLibreta() {
    this.mostrarModalEditarLibreta = true;
  }

  cerrarModalEditarLibreta() {
    this.mostrarModalEditarLibreta = false;
  }

  guardarCambiosLibreta() {
    this.mostrarModalEditarLibreta = false;
  }

  // Estudios
  abrirModalAgregarEstudio() {
    this.nuevoEstudio = {}; // limpiar
    this.mostrarModalAgregarEstudio = true;
  }

  cerrarModalAgregarEstudio() {
    this.mostrarModalAgregarEstudio = false;
  }

  guardarNuevoEstudio() {
    this.estudios.push({ ...this.nuevoEstudio, abierto: false });
    this.cerrarModalAgregarEstudio();
  }

  eliminarEstudio(index: number) {
    this.estudios.splice(index, 1);
  }

  toggleEstudio(index: number) {
    this.estudios[index].abierto = !this.estudios[index].abierto;
  }

  editarEstudio(index: number) {
    this.nuevoEstudio = { ...this.estudios[index] };
    this.eliminarEstudio(index);
    this.mostrarModalAgregarEstudio = true;
  }

  // Experiencia
  abrirModalAgregarExperiencia() {
    this.nuevaExperiencia = {};
    this.mostrarModalAgregarExperiencia = true;
  }

  cerrarModalAgregarExperiencia() {
    this.mostrarModalAgregarExperiencia = false;
  }

  guardarNuevaExperiencia() {
    this.experiencias.push({ ...this.nuevaExperiencia, abierto: false });
    this.cerrarModalAgregarExperiencia();
  }

  eliminarExperiencia(index: number) {
    this.experiencias.splice(index, 1);
  }

  toggleExperiencia(index: number) {
    this.experiencias[index].abierto = !this.experiencias[index].abierto;
  }

  editarExperiencia(index: number) {
    this.nuevaExperiencia = { ...this.experiencias[index] };
    this.eliminarExperiencia(index);
    this.mostrarModalAgregarExperiencia = true;
  }
}