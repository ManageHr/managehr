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
    return contratos.filter(c => (c?.nombreUsuario ?? '').toLowerCase().includes(filtroLower));
  }
}
