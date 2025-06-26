import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MenuComponent } from '../menu/menu.component';
import { HojaDeVidaService } from 'src/app/services/hoja-de-vida.service';
import { EstudiosService } from 'src/app/services/estudios.service';
import { ExperienciaService } from 'src/app/services/experiencia.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-hoja-de-vida',
  standalone: true,
  templateUrl: './hoja-de-vida.component.html',
  styleUrls: ['./hoja-de-vida.component.scss'],
  imports: [CommonModule, FormsModule, MenuComponent]
})
export class HojaDeVidaComponent implements OnInit {
  hojaDeVida: any = {};
  estudios: any[] = [];
  experiencias: any[] = [];

  mostrarModalEditarLibreta = false;
  mostrarModalAgregarEstudio = false;
  mostrarModalAgregarExperiencia = false;

  nuevoEstudio: any = {};
  nuevaExperiencia: any = {};

  usuario: any;
  idHojaDeVida: number | null = null;

  constructor(
    private hojaDeVidaService: HojaDeVidaService,
    private estudiosService: EstudiosService,
    private experienciaService: ExperienciaService
  ) {}

  ngOnInit(): void {
    const usuarioString = localStorage.getItem('usuario');
    if (usuarioString) {
      this.usuario = JSON.parse(usuarioString);
      this.cargarHojaDeVida();
    } else {
      Swal.fire('Usuario no encontrado', 'Debes iniciar sesión nuevamente', 'warning');
    }
  }

  cargarHojaDeVida() {
    this.hojaDeVidaService.getHojaDeVidaPorDocumento(this.usuario?.perfil?.numDocumento).subscribe({
      next: (res) => {
        this.hojaDeVida = res.hojaDeVida;
        this.idHojaDeVida = res.hojaDeVida?.idHojaDeVida;
        this.cargarEstudios();
        this.cargarExperiencias();
      },
      error: (err) => {
        console.error('❌ Error al cargar la hoja de vida', err);
        Swal.fire('Error', 'No se pudo cargar la hoja de vida', 'error');
      }
    });
  }

  cargarEstudios() {
    if (!this.idHojaDeVida) return;
    this.estudiosService.getPorHojaDeVida(this.idHojaDeVida).subscribe({
      next: (res) => {
        this.estudios = res.estudios.map((e: any) => ({
          ...e.idEstudios,
          abierto: false,
          idRelacion: e.id
        }));
      },
      error: (err) => {
        console.error('❌ Error al cargar estudios', err);
        Swal.fire('Error', 'No se pudieron cargar los estudios', 'error');
      }
    });
  }

  cargarExperiencias() {
    if (!this.idHojaDeVida) return;
    this.experienciaService.getPorHojaDeVida(this.idHojaDeVida).subscribe({
      next: (res) => {
        this.experiencias = res.experiencias.map((exp: any) => ({
          ...exp,
          abierto: false
        }));
      },
      error: (err) => {
        console.error('❌ Error al cargar experiencias', err);
        Swal.fire('Error', 'No se pudieron cargar las experiencias', 'error');
      }
    });
  }

  abrirModalEditarLibreta() {
    this.mostrarModalEditarLibreta = true;
  }

  cerrarModalEditarLibreta() {
    this.mostrarModalEditarLibreta = false;
  }

  guardarCambiosLibreta() {
    if (!this.hojaDeVida.idHojaDeVida) return;

    const payload = {
      claseLibretaMilitar: this.hojaDeVida.claseLibretaMilitar,
      numeroLibretaMilitar: this.hojaDeVida.numeroLibretaMilitar,
      usuarioNumDocumento: this.hojaDeVida.usuarioNumDocumento
    };

    this.hojaDeVidaService.actualizarHojaDeVida(this.hojaDeVida.idHojaDeVida, payload).subscribe({
      next: () => {
        this.cerrarModalEditarLibreta();
        Swal.fire('Actualizado', 'La libreta militar fue actualizada correctamente', 'success');
        this.cargarHojaDeVida();
      },
      error: (err) => {
        console.error('❌ Error al actualizar libreta militar', err);
        Swal.fire('Error', 'No se pudo actualizar la libreta militar', 'error');
      }
    });
  }

  abrirModalAgregarEstudio() {
    this.nuevoEstudio = {};
    this.mostrarModalAgregarEstudio = true;
  }

  cerrarModalAgregarEstudio() {
    this.mostrarModalAgregarEstudio = false;
  }

  guardarNuevoEstudio() {
    if (!this.usuario?.numDocumento) return;

    const payload = {
      ...this.nuevoEstudio,
      numDocumento: this.usuario.numDocumento,
      estado: true
    };

    this.estudiosService.create(payload).subscribe({
      next: () => {
        this.cerrarModalAgregarEstudio();
        Swal.fire('Estudio agregado', 'El estudio fue registrado correctamente', 'success');
        this.cargarEstudios();
      },
      error: (err) => {
        console.error('❌ Error al guardar estudio', err);
        Swal.fire('Error', 'No se pudo guardar el estudio', 'error');
      }
    });
  }

  eliminarEstudio(index: number) {
    const idRelacion = this.estudios[index].idRelacion;
    this.estudiosService.delete(idRelacion).subscribe({
      next: () => {
        Swal.fire('Eliminado', 'El estudio ha sido eliminado', 'success');
        this.cargarEstudios();
      },
      error: (err) => {
        console.error('❌ Error al eliminar estudio', err);
        Swal.fire('Error', 'No se pudo eliminar el estudio', 'error');
      }
    });
  }

  toggleEstudio(index: number) {
    this.estudios[index].abierto = !this.estudios[index].abierto;
  }

  editarEstudio(index: number) {
    this.nuevoEstudio = { ...this.estudios[index] };
    this.eliminarEstudio(index); // opcional
    this.mostrarModalAgregarEstudio = true;
  }

  abrirModalAgregarExperiencia() {
    this.nuevaExperiencia = {};
    this.mostrarModalAgregarExperiencia = true;
  }

  cerrarModalAgregarExperiencia() {
    this.mostrarModalAgregarExperiencia = false;
  }

  guardarNuevaExperiencia() {
    if (!this.idHojaDeVida) return;

    const payload = {
      ...this.nuevaExperiencia,
      idHojaDeVida: this.idHojaDeVida
    };

    this.experienciaService.create(payload).subscribe({
      next: () => {
        this.cerrarModalAgregarExperiencia();
        Swal.fire('Experiencia guardada', 'La experiencia fue registrada correctamente', 'success');
        this.cargarExperiencias();
      },
      error: (err) => {
        console.error('❌ Error al guardar experiencia', err);
        Swal.fire('Error', 'No se pudo guardar la experiencia', 'error');
      }
    });
  }

  eliminarExperiencia(index: number) {
    const idExp = this.experiencias[index].id;
    this.experienciaService.delete(idExp).subscribe({
      next: () => {
        Swal.fire('Eliminado', 'La experiencia ha sido eliminada', 'success');
        this.cargarExperiencias();
      },
      error: (err) => {
        console.error('❌ Error al eliminar experiencia', err);
        Swal.fire('Error', 'No se pudo eliminar la experiencia', 'error');
      }
    });
  }

  toggleExperiencia(index: number) {
    this.experiencias[index].abierto = !this.experiencias[index].abierto;
  }

  editarExperiencia(index: number) {
    this.nuevaExperiencia = { ...this.experiencias[index] };
    this.eliminarExperiencia(index); // opcional
    this.mostrarModalAgregarExperiencia = true;
  }
}
