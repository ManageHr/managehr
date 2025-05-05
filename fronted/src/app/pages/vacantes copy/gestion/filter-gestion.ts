// En tu archivo: src/app/pages/vacantes copy/gestion/filter-gestion.ts

import { Pipe, PipeTransform } from '@angular/core';
// Importa la Interfaz Vacante (que DEBE tener 'nomVacante', 'idVacantes', etc.)
import { Vacante } from '../../../services/gestion.service'; // <-- Verifica esta ruta

@Pipe({
  name: 'filterVacante', // Nombre que usas en el HTML
  standalone: true
})
export class FilterVacantePipe implements PipeTransform {

  transform(vacantes: Vacante[] | null | undefined, filtro: string): Vacante[] {
    // Maneja input null/undefined o filtro vacío
    if (!vacantes || !filtro) {
      return vacantes || [];
    }

    const lowerCaseFiltro = filtro.toLowerCase();

    // *** LÓGICA DE FILTRADO CORREGIDA: Ahora usa 'nomVacante' y elimina 'codigo' ***
    return vacantes.filter(vacante => {
       // Filtra solo por el NUEVO nombre de la vacante: nomVacante
       // Usamos ?. para acceso seguro si nomVacante pudiera ser null/undefined en los datos
       return vacante.nomVacante?.toLowerCase().includes(lowerCaseFiltro);

       // Si quieres filtrar por otros campos de la imagen (ej: descripVacante, cargoVacante), añádelos así:
       // return vacante.nomVacante?.toLowerCase().includes(lowerCaseFiltro) ||
       //        vacante.descripVacante?.toLowerCase().includes(lowerCaseFiltro) ||
       //        vacante.cargoVacante?.toLowerCase().includes(lowerCaseFiltro);

       // Si por alguna razón 'codigo' sigue siendo relevante en tus datos y quieres buscar por él
       // bajo otro nombre (ej: si se mapea a 'idVacantes' o algo así), tendrías que ajustar aquí.
       // Pero basándonos en la imagen, 'nomVacante' es el nombre de la vacante.
    });
    // *** FIN LÓGICA DE FILTRADO CORREGIDA ***
  }
}