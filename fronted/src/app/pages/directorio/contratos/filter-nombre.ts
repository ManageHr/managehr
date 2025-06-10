import { Pipe, PipeTransform } from '@angular/core';
import { ContratosService, Contratos} from '../../../services/contratos.service';

@Pipe({
  name: 'filterNombre',
  standalone: true
})
export class FilterNombre implements PipeTransform {
  transform(contratos: Contratos[], filtro: string): Contratos[] {
    if (!filtro || !contratos) return contratos;

    const filtroLower = filtro.toLowerCase();

    return contratos.filter(c => {
      // Asegurarse de que el usuario exista
      const primerNombre = c.hoja_de_vida.usuario?.primerNombre || '';
      const primerApellido = c.hoja_de_vida.usuario?.primerApellido || '';

      const nombreCompleto = `${primerNombre} ${primerApellido}`.toLowerCase();

      return nombreCompleto.includes(filtroLower);
    });
  }
}
