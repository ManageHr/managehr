import { Pipe, PipeTransform } from '@angular/core';
// === CORREGIR LA RUTA DE IMPORTACIÓN DE Postulacion ===
// Importar Postulacion desde el archivo del servicio donde está definida y exportada
import { Postulacion } from '../../../services/postulaciones.service';

@Pipe({
  name: 'filterPostulacion', // El nombre que usas en el HTML
  standalone: true // O false si no es standalone
})
export class FilterPostulacionPipe implements PipeTransform {

  // Este pipe toma un array de postulaciones y un término de filtro (filtroNombre).
  // Si el filtroNombre está destinado a ser un Vacante ID, este pipe intentará
  // filtrar la lista en el frontend basándose en el vacantesId.

  transform(postulaciones: Postulacion[], filtroTerm: string): Postulacion[] {
    // Si no hay término de filtro, devuelve el array original completo
    if (!filtroTerm || filtroTerm.trim() === '') {
      return postulaciones;
    }

    // Convertir el término de filtro a minúsculas para una búsqueda insensible a mayúsculas/minúsculas
    const lowerTerm = filtroTerm.toLowerCase();

    // === Lógica de Filtrado Cliente ===
    // Vamos a asumir que este pipe filtra la lista por el campo 'vacantesId'
    // convirtiéndolo a string para usar 'includes'.
    // Si necesitas filtrar por otro campo (ej. estado, fecha), la lógica aquí debería cambiar.

    return postulaciones.filter(postulacion => {
      // Asegurarse de que 'vacantesId' existe y convertirlo a string para usar includes
      // Usamos el nombre de campo correcto de la interfaz (vacantesId)
      const vacanteIdStr = postulacion.vacantesId != null ? postulacion.vacantesId.toString() : '';

      // Verificar si el vacantesId (como string) incluye el término de filtro
      return vacanteIdStr.includes(lowerTerm);

      // Si quisieras filtrar por el estado en el frontend (ej. 'pendiente', 'aceptado'):
      // return postulacion.estado.toLowerCase().includes(lowerTerm);

      // O si quisieras buscar en múltiples campos:
      // return vacanteIdStr.includes(lowerTerm) || postulacion.estado.toLowerCase().includes(lowerTerm);
    });
  }
}