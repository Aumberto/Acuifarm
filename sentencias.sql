-- Calcular las entradas de pienso semanales

Select week(pedidos.fecha_descarga, 3),  pedidos_detalles.codigopienso, pedidos_detalles.pienso, sum(pedidos_detalles.cantidad)
  from pedidos_detalles, pedidos
 where pedidos.id = pedidos_detalles.pedido_id
   and pedidos.estado = 'En preparación' 
   group by week(pedidos.fecha_descarga, 3), pedidos_detalles.codigopienso, pedidos_detalles.pienso


-- Calcular consumos semanales

    Select week(consumos.fecha, 3), consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso, sum(consumos.cantidad)
      from consumos
  group by week(consumos.fecha, 3), consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso

    Select tamanio_pellets.diametro, week, year, sum(cantidad)
      from semanas right join consumos on semanas.week = week(consumos.fecha, 3)
                   left join tamanio_pellets on tamanio_pellets.diametro = consumos.diametro_pienso
      where year=2014
        and (week >= 24 and week <=29)
        and tamanio_pellets.proveedor_pienso_id=1
        group by year, week, tamanio_pellets.diametro

     Select tamanio_pellets.diametro, week, year, sum(cantidad)
      from  semanas left join consumos on semanas.week = week(consumos.fecha, 3) and year=2014 and (week >= 30 and week <=33)
                    left join tamanio_pellets on tamanio_pellets.diametro = consumos.diametro_pienso and tamanio_pellets.proveedor_pienso_id=1
      where year=2014 and (week >= 30 and week <=33)
      group by year, week, tamanio_pellets.diametro

      Select tamanio_pellets.diametro, sum(cantidad)
        from tamanio_pellets left join tamanio_pellets.diametro = consumos.diametro_pienso and tamanio_pellets.proveedor_pienso_id=1
    group by tamanio_pellets.diametro

      select year,  week, diametro, ifnull(sum(cantidad),0)
      from tamanio_pellets left join consumos on tamanio_pellets.diametro = consumos.diametro_pienso  and tamanio_pellets.proveedor_pienso_id=1
                           left join semanas  on semanas.week = week(consumos.fecha, 3) and year=2014
      group by year,  week, diametro
      order by year,  week, diametro



     Select tamanio_pellets.diametro, week(consumos.fecha, 3), sum(cantidad)
       from tamanio_pellets left join consumos on tamanio_pellets.diametro = consumos.diametro_pienso
       where tamanio_pellets.proveedor_pienso_id=1
       group by year, week, tamanio_pellets.diametro

       Select year,week,consumos.diametro_pienso,sum(cantidad)
         from semanas left join consumos on semanas.week = week(consumos.fecha, 3)
         where year=2014
           and (week >= 24 or week <=29)
           and consumos.proveedor_id = 1
           group by year, week, diametro_pienso


           select year, week, diametro, sum(cantidad)
           from consumos left join semanas on semanas.week = week(consumos.fecha, 3)
                         left join tamanio_pellets on tamanio_pellets.diametro = consumos.diametro_pienso
           where (week >= 24 and week <=49)
            and year =2014
            group by year, week, diametro
-- Son bastante buenas
Select week(consumos.fecha, 3), tamanio_pellets.proveedor_pienso_id, consumos.proveedor_id, proveedores_pienso.nombre,  tamanio_pellets.diametro, ifnull(sum(consumos.cantidad),0)
 from consumos right join tamanio_pellets on consumos.proveedor_id =  tamanio_pellets.proveedor_pienso_id and consumos.diametro_pienso = tamanio_pellets.diametro and consumos.fecha >= '2014-09-01' and consumos.fecha <= '2014-09-07'
               inner join proveedores_pienso on proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
 group by week(consumos.fecha, 3), tamanio_pellets.proveedor_pienso_id, tamanio_pellets.diametro
 order by tamanio_pellets.proveedor_pienso_id,  tamanio_pellets.diametro, week(consumos.fecha, 3)

 
 Select week(consumos.fecha, 3), consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso, sum(consumos.cantidad)
 from consumos
 where consumos.fecha >= '2014-08-27' and consumos.fecha <= '2014-09-02'
 group by week(consumos.fecha, 3), consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso
 order by consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso, week(consumos.fecha, 3)

-- Comprobar los consumos entre dos fechas
 Select consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso, sum(consumos.cantidad)
 from consumos
 where consumos.fecha >= '2014-11-24' and consumos.fecha <= '2014-12-30' and jaula in ('M002')
 group by consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso
 order by consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso

-- Comprobar los consumos entre dos fechas de las jaulas
  Select jaula, consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso,  sum(consumos.cantidad)
 from consumos
 where consumos.fecha >= '2014-08-27' and consumos.fecha <= '2014-09-02' and jaula in ('J004', 'J011', 'J012','J017','M001', 'P004', 'P009', 'P011')
 group by jaula, consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso
 order by jaula, consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso

-- Comprobar los consumos entre dos fechas de las jaulas
  Select jaula, fecha, consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso,  sum(consumos.cantidad)
 from consumos
 where consumos.fecha >= '2014-11-24' and consumos.fecha <= '2014-12-30' and jaula in ('M002')
 group by jaula, fecha, consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso
 order by jaula, fecha, consumos.proveedor_id, consumos.proveedor, consumos.diametro_pienso



-- Comprobar el stock_inicial 
Select 'real', date, unitname, groupid, stock_count_ini, stock_avg_ini, stock_bio_ini, stock_count_fin, stock_avg_fin, stock_bio_fin, feeduse, stock_bio_ini,
       (feeduse/stock_bio_ini)*100 as SFR, '0', '0', '0'
  from produccion_real 
  where unitname in ('J003')
  and date >= '2014-09-01'
  and date <= '2014-09-20'
union
Select 'simulado', date, unitname, groupid, stock_count_ini, stock_avg_ini, stock_bio_ini, stock_count_fin, stock_avg_fin, stock_bio_fin, cantidad_toma, stock_bio_ini, 
       (cantidad_toma/stock_bio_ini)*100 as SFR, cantidad_toma_modelo, cantidad_toma, porcentaje_toma
  from produccion_simulado
  where unitname in ('J003')
  and date not in (select distinct date from produccion_real)
  and date >= '2014-09-01'
  and date <= '2014-09-20'
  order by unitname, date 


  Select jaulas.nombre, granjas.nombre
  from jaulas, granjas
  where jaulas.granja_id = granjas.id
  and granjas.nombre = 'Melenara'
  order by granjas.nombre


  select jaulas.nombre, jaulas.id, ps.stock_count_ini
  from jaulas right OUTER JOIN produccion_simulado as ps on jaulas.nombre = ps.unitname
  where jaulas.granja_id = 1
  and ps.date = '2014-07-22'
  order by jaulas.nombre

  select jaulas.nombre, ps.groupid, ps.stock_count_ini, ps.stock_avg_ini, ps.stock_bio_ini,
         ps.cantidad_toma_modelo, ps.sfr
  from produccion_simulado ps right join jaulas on ps.unitname = jaulas.nombre
  and ps.date = '2014-07-23'
  where jaulas.granja_id = 1


  select jaulas.nombre, consumos.lote, consumos.proveedor, consumos.diametro_pienso, max(consumos.cantidad_recomendada), sum(consumos.cantidad)
    from consumos right join jaulas on consumos.jaula = jaulas.nombre and consumos.fecha >= '2014-07-28' and consumos.fecha <= '2014-08-03'
   where jaulas.granja_id = 1
   group by jaulas.nombre, consumos.lote, consumos.proveedor, consumos.diametro_pienso
   order by jaulas.nombre


  select jaulas.nombre, consumos.lote, consumos.proveedor, consumos.diametro_pienso, max(consumos.cantidad_recomendada), sum(consumos.cantidad)
      from consumos right join jaulas on consumos.jaula = jaulas.nombre and consumos.fecha = '2014-07-24' 
   where jaulas.granja_id = 1
   group by jaulas.nombre, consumos.lote, consumos.proveedor, consumos.diametro_pienso
   order by jaulas.nombre

-- Para comprobar que estamos actualizando bien los datos de alimentación 
   Select granja, jaula, fecha, proveedor, pienso, diametro_pienso, cantidad_recomendada, porcentaje_estrategia, cantidad 
     from consumos
    where consumos.fecha >= '2014-08-21' and consumos.fecha <= '2014-09-04' and jaula in ('J004')
 order by fecha, jaula


   select date, groupid, stock_count_ini, stock_avg_ini, stock_bio_ini, stock_count_fin, stock_avg_fin, 
          stock_count_fin, FCR, SGR, SFR, porcentaje_toma, cantidad_toma_modelo, cantidad_toma 
     from produccion_simulado
    where unitname = 'J004'
    and date >= '2014-08-21' 
    and date <= '2014-09-04'
    order by date
      --and date >= '2014-07-28' 
      --and date <= '2014-08-03'


      


--Actualizar los datos iniciales de la tabla de producción_real
  Update produccion_real pr1, produccion_real pr2
     set pr1.stock_count_ini = pr2.stock_count_fin,
         pr1.stock_avg_ini = pr2.stock_avg_fin,
         pr1.stock_bio_ini = pr2.stock_bio_fin
   where pr2.unitname =  pr1.unitname
     and pr2.groupid  =  pr1.groupid
     and pr1.date     =  DATE_ADD(pr2.date, INTERVAL 1 DAY)
     


Select date, unitname, stock_count_ini, stock_count_fin, stock_avg_ini, stock_avg_fin, stock_bio_ini, stock_bio_fin, feeduse
from produccion_real
where unitname = 'J001'
order by date

--Comparativa de consumo real vs consumo propuesto vs consumo del cantidad_toma_modelo
Select pr.date,pr.groupid as lote, pr.feeduse as consumo_real, ps.cantidad_toma_modelo as consumo_modelo, ps.cantidad_toma as consumo_propuesta, ps.porcentaje_toma as porcentaje_propuestaVsModelo,
      ((pr.feeduse/ps.cantidad_toma_modelo)*100) as porcentaje_realVsModelo
from produccion_real pr, produccion_simulado ps
where pr.unitname = ps.unitname
  and pr.date     = ps.date
  and pr.groupid  = ps.groupid
  and pr.unitname = 'J001'
  order by pr.date



  Select ps.date ,ps.groupid as lote, ifnull(pr.feeduse,0) as consumo_real, ps.cantidad_toma_modelo as consumo_modelo, 
         ps.cantidad_toma as consumo_propuesta, ps.porcentaje_toma as porcentaje_propuestaVsModelo,
         ifnull(((pr.feeduse/ps.cantidad_toma_modelo)*100),0) as porcentaje_realVsModelo
    from produccion_real pr left join produccion_simulado ps on pr.date     = ps.date
                                                             and pr.groupid  = ps.groupid
                                                             and pr.unitname = ps.unitname
                                                             
   where ps.unitname = 'J001'
     and ps.date     >= '2014-08-10'
     and ps.date     <= '2014-08-25' 
  order by ps.date



Select ps.date, ps.groupid as lote, ps.cantidad_toma_modelo as consumo_modelo, 
       ps.cantidad_toma as consumo_propuesta, ps.porcentaje_toma as porcentaje_propuestaVsModelo
  from produccion_simulado ps
where ps.unitname = 'J001'
     and ps.date     >= '2014-08-10'
     and ps.date     <= '2014-08-25' 
  order by ps.date


select * 
  from produccion_simulado 
 where unitname='J002' 
   and date     >= '2014-08-28'
   and date     <= '2014-09-05'
   order by date


      Select granja, jaula, fecha, proveedor, pienso, diametro_pienso, cantidad_recomendada, porcentaje_estrategia, cantidad 
     from consumos
    where consumos.fecha >= '2014-08-30' and consumos.fecha <= '2014-09-20' and jaula in ('J001')
 order by fecha, jaula

       Select *
     from consumos
    where consumos.fecha >= '2014-08-19' and consumos.fecha <= '2014-09-01' and jaula in ('J004')
 order by fecha, jaula

        Select *
     from consumos
    where  jaula in ('J011')
      and consumos.fecha >= '2014-09-01'
 order by fecha, jaula


select jaulas.nombre, consumos.lote, consumos.proveedor, consumos.diametro_pienso, 
       min(consumos.cantidad_recomendada) as cantidad_recomendada, min(consumos.cantidad) as cantidad
  from consumos right join jaulas on consumos.jaula = jaulas.nombre and consumos.fecha >= '2014-08-21' and consumos.fecha <= '2014-09-04'
 where jaulas.granja_id = 1
 group by jaulas.nombre, consumos.lote, consumos.proveedor, consumos.diametro_pienso
 order by jaulas.nombre

  (Select c.jaula, c.lote, c.proveedor, c.diametro_pienso, min(ps.porcentaje_toma) as porcentaje_toma, 
          min(ps.cantidad_toma_modelo) as cantidad_toma_modelo, min(ps.cantidad_toma) as cantidad_toma
      from consumos c , produccion_simulado ps  
      where c.fecha >= '2014-08-21' 
        and c.fecha <= '2014-09-04'
        and c.jaula = ps.unitname 
        and c.fecha = ps.date
        and c.granja = ps.site
        and c.granja_id = 1
        group by c.jaula, c.lote, c.proveedor, c.diametro_pienso) as vista_consumos

  -- Perfecto para mostrar los resultados de la propuesta
  Select jaulas.nombre, vista_consumos.lote, vista_consumos.proveedor, vista_consumos.diametro_pienso, 
         vista_consumos.porcentaje_toma, vista_consumos.cantidad_toma_modelo, vista_consumos.cantidad_toma
  from (Select c.jaula, c.lote, c.proveedor, c.diametro_pienso, min(ps.porcentaje_toma) as porcentaje_toma, 
          min(ps.cantidad_toma_modelo) as cantidad_toma_modelo, min(ps.cantidad_toma) as cantidad_toma
      from consumos c , produccion_simulado ps  
      where c.fecha >= '2014-08-21' 
        and c.fecha <= '2014-09-04'
        and c.jaula = ps.unitname 
        and c.fecha = ps.date
        and c.granja = ps.site
        and c.granja_id = 1
        group by c.jaula, c.lote, c.proveedor, c.diametro_pienso) vista_consumos right join jaulas on vista_consumos.jaula = jaulas.nombre 
  where jaulas.granja_id = 1
order by jaulas.nombre, vista_consumos.diametro_pienso
  --- Genial!!!!
  

    Select c.lote, c.proveedor, c.diametro_pienso
    from consumos c
    where c.fecha >= '2014-08-21' and c.fecha <= '2014-09-04'

    select ps.porcentaje_toma, ps.cantidad_toma_modelo, ps.cantidad_toma
    from produccion_simulado ps
    where ps.date >= '2014-08-21' and ps.date <= '2014-09-04'
    and unitname = 'J001'

-- Consulta para seleccionar el tamaño de pellet que le corresponde a un lote de pez, dependiendo del peso medio 
-- que tiene y seleccionando el proveedor de pienso

Select *
  from tamanio_pellets
  where pm_min <= 709.2
        and pm_max >  709.2
    and proveedor_pienso_id = 2

-- Para localizar si un lote empieza el transito de pienso o no
Select * 
  from  tamanio_pellets
  where (transito <= 115.43 and transito+1 > 115.43 and proveedor_pienso_id = 1) or (pm_min <= 115.43 and pm_max >  115.43 and proveedor_pienso_id = 1) 
  order by diametro

Select * 
  from  tamanio_pellets
  where (transito <= 25 and transito+1 > 25 and proveedor_pienso_id = 1) 
  order by diametro
------------------------------------------------------------------------------------------------------------------------------------------------------------
 

-- Comprobar que está en la tabla de tránsito
Select * from control_transito
where jaula = 'J002'
and lote = '1407'
and fecha_inicial <= '2014-09-02'
and fecha_final >= '2014-09-02'

-----------------------------------------------------------
-- Imprimir listado de rangos

Select cr.nombre, tp.diametro, pp.nombre as proveedor_pienso, dr.pm_min, dr.pm_transito, dr.pm_max 
  from cabecera_rangos cr, detalle_rangos dr, tamanio_pellets tp, proveedores_pienso pp
 where cr.id = dr.cabecera_rango_id
   and dr.tamanio_pellet_id = tp.id
   and tp.proveedor_pienso_id = pp.id
order by cr.predeterminado desc, cr.nombre, dr.pm_min

-- Obtener el pienso que le corresponde a un pez con un peso medio determinado. 
-- Usamos las tablas de rangos para probar el sistem

Select * 
  from detalle_rangos rd 
 where cabecera_rango_id = 3
   and pm_min <= 9
   and pm_transito > 9

Select * 
  from detalle_rangos rd 
 where (pm_max >  11 and pm_transito <= 11 and cabecera_rango_id = 3)
    or (pm_min >= 11 and pm_transito >  11 and cabecera_rango_id = 3)
    order by pm_min limit 2
------------------------------------------- A partir de aquí es todo basura para eliminar ------------------

Select fecha, proveedor, granja, diametro_pienso, pienso_id, sum(cantidad) as consumo_real 
  from consumo_real
group by fecha, proveedor, granja, diametro_pienso, pienso_id
order by fecha, proveedor, granja, diametro_pienso, pienso_id
Union 
Select 'simulado', fecha, proveedor, diametro_pienso, pienso_id, sum(cantidad) as consumo_real 
  from consumos
  where fecha not in (select fecha from consumo_real)
group by fecha, proveedor, diametro_pienso, pienso_id
order by fecha, proveedor, diametro_pienso, pienso_id

Select fecha, proveedor, diametro_pienso, pienso_id, sum(cantidad) as consumo_simulado, 
       (select sum(cantidad) 
        from consumo_real
        where fecha = consumos.fecha
          and pienso_id = consumos.pienso_id
          group by fecha, pienso_id ) as consumo_real
from consumos
group by fecha, proveedor, diametro_pienso, pienso_id
order by fecha, proveedor, diametro_pienso, pienso_id

-- Consumos acumulados hasta la fecha. Reales
Select fecha, pienso_id, pienso, codigo_pienso, sum(cantidad) as consumo, (select ifnull(sum(cantidad),0) 
                                                                  from consumo_real cr
                                                                  where fecha < cr1.fecha and pienso_id = cr1.pienso_id
                                                                  ) as consumo_acumulado, 
                                                                (select ifnull(sum(cantidad), 0)
                                                                  from pedidos_detalles, pedidos
                                                                 where pedidos.id = pedidos_detalles.pedido_id
                                                                   and pedidos.estado = 'Descargado' 
                                                                   and pedidos.fecha_descarga < cr1.fecha
                                                                   and pedidos_detalles.pienso_id = cr1.pienso_id) as entradas_acumuladas,  (select ifnull(sum(cantidad), 0)
                                                                  from pedidos_detalles, pedidos
                                                                 where pedidos.id = pedidos_detalles.pedido_id
                                                                   and pedidos.estado = 'Descargado' 
                                                                   and pedidos.fecha_descarga < cr1.fecha
                                                                   and pedidos_detalles.pienso_id = cr1.pienso_id) - (select ifnull(sum(cantidad),0) 
                                                                  from consumo_real cr
                                                                  where fecha < cr1.fecha and pienso_id = cr1.pienso_id
                                                                  ) - sum(cantidad)
from consumo_real cr1
where fecha <= '2014-09-14' and fecha > '2014-09-10'
and pienso_id = 11
group by fecha, pienso_id, pienso, codigo_pienso
order by fecha, pienso_id, pienso, codigo_pienso

-- Stock de cada pienso en cada uno de los almacenes
Select almacenes.nombre, piensos.id, piensos.nombre, sum(cantidad), max(fecha)
  from movimientos_almacenes, almacenes, piensos
  where almacenes.id = movimientos_almacenes.almacen_id
    and piensos.id   = movimientos_almacenes.pienso_id
    and movimientos_almacenes.fecha <= '2014-09-11'
  group by almacenes.nombre, piensos.id, piensos.nombre


-- stock real de cada pienso en todas las granjas
Select pp.nombre, tp.diametro, sum(cantidad), max(fecha)
  from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
  where a.id = ma.almacen_id
    and p.id   = ma.pienso_id
    and p.diametro_pellet_id = tp.id
    and ma.fecha <= '2014-09-10'
    and pp.id = p.proveedor_id
  group by pp.nombre, tp.diametro

-- stock real de cada pienso en un almacen concreto
Select a.nombre, pp.nombre, tp.diametro, sum(cantidad), max(fecha)
  from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
  where a.id = ma.almacen_id
    and p.id   = ma.pienso_id
    and p.diametro_pellet_id = tp.id
    and ma.fecha <= '2014-09-10'
    and pp.id = p.proveedor_id
  group by a.nombre, pp.nombre, tp.diametro

-- Consumo simulado de cada tipo de pienso en todas las granjas
Select week(fecha, 1), pp.nombre, tp.diametro, sum(cantidad)
  from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
 where c.proveedor_id = pp.id
   and c.pienso_id = p.id
   and tp.id = p.diametro_pellet_id
   and fecha > '2014-09-10'
   and week(fecha, 1) = 37
   group by week(fecha, 1), pp.nombre, tp.diametro

-- Consumo simulado de cada tipo de pienso en cada granja todas las granjas y por dia
Select fecha, c.granja, pp.nombre, tp.diametro, sum(cantidad)
  from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
 where c.proveedor_id = pp.id
   and c.pienso_id = p.id
   and tp.id = p.diametro_pellet_id
   and fecha >= '2014-10-07' and fecha <= DATE_ADD('2014-10-07', INTERVAL 1 DAY)
   group by fecha, c.granja, pp.nombre, tp.diametro

-- Entradas previstas de piensos.
Select pp.nombre, tp.diametro, sum(cantidad)
  from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
 where pd.pedido_id = p.id  
   and pd.pienso_id = ps.id
   and tp.id = ps.diametro_pellet_id
   and ps.proveedor_id = pp.id
   and p.fecha_descarga > '2014-09-10'
   and week(p.fecha_descarga, 1) = 37
   and p.estado <> 'Descargado'
   group by pp.nombre, tp.diametro

-- Consulta para obtener el stock semana a semana
Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
       ifnull((Select sum(cantidad)
                 from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                where a.id = ma.almacen_id
                  and p.id   = ma.pienso_id
                  and p.diametro_pellet_id = tp.id
                  and ma.fecha <= '2014-09-10'
                  and pp.id = p.proveedor_id
                  and tamanio_pellets.id = tp.id
             group by pp.nombre, tp.diametro),0) as stock_real, 
       ifnull((Select sum(cantidad)
                 from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                where c.proveedor_id = pp.id
                  and c.pienso_id = p.id
                  and tp.id = p.diametro_pellet_id
                  and tamanio_pellets.id = tp.id
                  and fecha > '2014-09-10'
                  and week(fecha, 1) = 37
             group by pp.nombre, tp.diametro),0) as consumo_simulado,
       ifnull((Select sum(cantidad)
                 from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                where tamanio_pellets.id = tp.id
                  and pd.pedido_id = p.id  
                  and pd.pienso_id = ps.id
                  and tp.id = ps.diametro_pellet_id
                  and ps.proveedor_id = pp.id
                  and p.fecha_descarga > '2014-09-10'
                  and week(p.fecha_descarga, 1) = 37
                  and p.estado <> 'Descargado'
             group by pp.nombre, tp.diametro ) ,0) as pedidos
  from proveedores_pienso, tamanio_pellets
 where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
 order by proveedores_pienso.nombre, tamanio_pellets.diametro

 -- Consulta para obtener el stock de un almacen diariamente (prueba con Melenara)
 Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
       ifnull((Select sum(cantidad)
                 from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                where a.id = ma.almacen_id
                  and a.id = 2
                  and p.id   = ma.pienso_id
                  and p.diametro_pellet_id = tp.id
                  and ma.fecha <= '2014-10-07'
                  and pp.id = p.proveedor_id
                  and tamanio_pellets.id = tp.id
             group by pp.nombre, tp.diametro),0) as stock_real, 
       ifnull((Select sum(cantidad)
                 from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                where c.proveedor_id = pp.id
                  and c.pienso_id = p.id
                  and c.granja_id = 1
                  and tp.id = p.diametro_pellet_id
                  and tamanio_pellets.id = tp.id
                  and fecha = DATE_ADD('2014-10-07', INTERVAL 1 DAY)
              group by pp.nombre, tp.diametro),0) as consumo_simulado,
       ifnull((Select sum(cantidad)
                 from traslados_detalles td, traslados t, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                where tamanio_pellets.id = tp.id
                  and td.traslado_id = t.id  
                  and td.pienso_id = ps.id
                  and tp.id = ps.diametro_pellet_id
                  and ps.proveedor_id = pp.id
                  and t.fecha_traslado = DATE_ADD('2014-10-07', INTERVAL 1 DAY)
                  and t.estado <> 'Descargado'
             group by pp.nombre, tp.diametro ) ,0) as pedidos
  from proveedores_pienso, tamanio_pellets
 where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
 order by proveedores_pienso.nombre, tamanio_pellets.diametro



 Select jaulas.nombre, vista_consumos.lote, vista_consumos.proveedor, vista_consumos.diametro_pienso, 
        vista_consumos.porcentaje_toma, vista_consumos.cantidad_toma_modelo as cantidad_recomendada, vista_consumos.cantidad_toma as cantidad, 
        (select jlr.cabecera_rango_id 
            from jaula_lote_rango jlr, jaulas j, lotes l
          where jlr.jaula_id = j.id
            and jlr.lote_id = l.id
            and jlr.fecha_inicio <= '2014-09-02'
            and j.nombre = jaulas.nombre
            and l.nombre = vista_consumos.lote 
            order by j.nombre, l.nombre, jlr.created_at desc
            limit 1) as cabecera_rango_id
 from (Select c.jaula, c.lote, c.proveedor, c.diametro_pienso, min(ps.porcentaje_toma) as porcentaje_toma, 
              min(ps.cantidad_toma_modelo) as cantidad_toma_modelo, min(ps.cantidad_toma) as cantidad_toma
         from consumos c , produccion_simulado ps  
       where c.fecha >= '2014-09-02' 
         and c.fecha <= '2014-09-15'
         and c.jaula = ps.unitname 
         and c.fecha = ps.date
         and c.granja = ps.site
         and c.granja_id = 2
     group by c.jaula, c.lote, c.proveedor, c.diametro_pienso) vista_consumos right join jaulas on vista_consumos.jaula = jaulas.nombre
where jaulas.granja_id = 2
order by jaulas.nombre, vista_consumos.diametro_pienso 



select jaulas.nombre, pr.groupid, pr.stock_count_ini, pr.stock_avg_ini, pr.stock_bio_ini,
       ps.cantidad_toma_modelo, ps.sfr
 from produccion_real pr right join jaulas on pr.unitname = jaulas.nombre
                                           and pr.date = '2014-09-17'
       left join produccion_simulado ps on  pr.unitname = ps.unitname 
                                       and  pr.site     = ps.site
                                       and  pr.date     = ps.date 
 where jaulas.granja_id = 1


select jaulas.nombre, pr.groupid, pr.stock_count_ini, pr.stock_avg_ini, pr.stock_bio_ini,
       ps.cantidad_toma_modelo, ps.sfr
 from produccion_real pr right join jaulas on pr.unitname = jaulas.nombre
                                           and pr.date = '2014-09-17'
where jaulas.granja_id = 1

Update produccion_real pr1, produccion_real pr2
     set pr1.stock_count_ini = pr2.stock_count_fin,
         pr1.stock_avg_ini = pr2.stock_avg_fin,
         pr1.stock_bio_ini = pr2.stock_bio_fin
   where pr2.unitname =  pr1.unitname
     and pr2.groupid  =  pr1.groupid
     and pr1.date     =  DATE_ADD(pr2.date, INTERVAL 1 DAY)


     select jaulas.nombre, pr.groupid, pr.stock_count_ini, pr.stock_avg_ini, pr.stock_bio_ini,
                                             ps.cantidad_toma_modelo, ps.sfr
                                        from produccion_real pr right join jaulas on pr.unitname = jaulas.nombre
                                                                                 and pr.date = ?
                                             left join produccion_simulado ps on  pr.unitname = ps.unitname 
                                                                              and  pr.site     = ps.site
                                                                              and  pr.date     = ps.date 
                                       where jaulas.granja_id = ?


    Select sum(stock_count_ini) as total_stock_ini, sum(stock_bio_ini) as total_bio_ini, 
           (sum(stock_bio_ini)/sum(stock_count_ini))*1000 as total_avg_ini, sum(feeduse) as cantidad_toma
      from produccion_real
      where date = '2014-09-16'
      and site = 'Melenara'
      group by site


Select week(fecha, 3), fecha, granja, granja_id, jaula, jaula_id, lote, lote_id, diametro_pienso, 
       max(stock_avg_ini), max(porcentaje_toma), max(cantidad_toma_modelo), sum(cantidad)
from consumos, produccion_simulado
where fecha=date
  and granja = site
  and unitname = jaula
  and groupid = lote 
  and week(fecha, 3) >= 40
group by  week(fecha, 3), fecha, granja, granja_id, jaula, jaula_id, lote, lote_id, diametro_pienso
order by fecha

select granja_id, granja, jaula_id, jaula, lote_id, lote, 
       stock_count_ini, stock_avg_ini, proveedor_id, 
       proveedor, pienso_id, pienso, codigo_pienso, diametro_pienso, 
       cantidad, porcentaje_estrategia, (cantidad/25) as Sacos
  from consumos, produccion_simulado
  where fecha = '2014-10-13'
    and granja = site
    and fecha = date
    and jaula = unitname
    and lote = groupid
  order by granja, jaula, lote, codigo_pienso asc


  Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
                                              ifnull((Select sum(cantidad)
                                                        from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                                                       where a.id = ma.almacen_id
                                                         and p.id   = ma.pienso_id
                                                         and p.diametro_pellet_id = tp.id
                                                         and ma.fecha <= '2014-11-05'
                                                         and pp.id = p.proveedor_id
                                                         and tamanio_pellets.id = tp.id
                                                    group by pp.nombre, tp.diametro),0) as stock_real, 
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > '2014-11-05'
                                                         and week(fecha, 3) = 49
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > '2014-11-05'
                                                         and fecha < '2014-12-01'
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado_acumulado,
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > '2014-11-05'
                                                         and fecha >= '2014-12-08' 
                                                         and fecha <= '2014-12-14' 
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado_siguiente_semana,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > '2014-11-05'
                                                         and week(p.fecha_descarga, 3) = 49
                                                         and p.estado <> 'Descargado'
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos, 
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > '2014-11-05'
                                                         and p.fecha_descarga < '2014-12-01'
                                                         and p.estado <> 'Descargado'
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos_acumulados
                                        from proveedores_pienso, tamanio_pellets
                                       where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                       order by proveedores_pienso.nombre, tamanio_pellets.diametro


                                       Select pp.nombre,p.fecha_descarga, sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where 10 = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > 2014-11-05
                                                         and week(p.fecha_descarga, 3) < 49
                                                         and p.estado <> 'Descargado'
                                                    group by pp.nombre, p.fecha_descarga, tp.diametro 

                                                    Select fecha, sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and 10 = tp.id
                                                         and ( fecha > '2014-11-05')
                                                         and fecha < '2014-11-09'
                                                         group by pp.nombre, fecha, tp.diametro


Select granja, jaula, lote, diametro_pienso, fecha, ps.stock_avg_ini, sum(cantidad)
  from consumos, produccion_simulado ps
 where fecha >= '2014-12-08' and fecha <= '2014-12-14'
   and diametro_pienso = 10.0 
   and ps.site = granja
   and ps.unitname = jaula
   and ps.groupid = lote
   and ps.date = fecha
 group by granja, jaula, lote, diametro_pienso, fecha, ps.stock_avg_ini


 select * from consumos where fecha >= '2014-11-24' and jaula = 'M002'

 Select * from produccion_simulado where date >= '2014-11-24' and unitname = 'M002'


 SELECT     Cobros.N_Factura, Cobros.Factura, dbo.OJDT.Number AS Operacion, dbo.ORCT.DocDate AS Fecha_Cobro, dbo.ORCT.DocNum AS Num_Cobro
FROM         dbo.OJDT INNER JOIN
                      dbo.ORCT ON dbo.OJDT.TransId = dbo.ORCT.TransId INNER JOIN
                          (SELECT     dbo.OINV.DocNum AS N_Factura, dbo.OINV.DocEntry AS Factura, MAX(OJDT_1.TransId) AS id_cobro
                            FROM          dbo.OJDT AS OJDT_1 INNER JOIN
                                                   dbo.RCT2 INNER JOIN
                                                   dbo.OINV ON dbo.RCT2.DocEntry = dbo.OINV.DocEntry INNER JOIN
                                                   dbo.ORCT AS ORCT_1 ON dbo.RCT2.DocNum = ORCT_1.DocNum ON OJDT_1.TransId = ORCT_1.TransId
                            WHERE      (YEAR(dbo.OINV.DocDate) = 2014) AND (dbo.RCT2.InvType = 13) AND (dbo.OINV.DocEntry IN
                                                       (SELECT     DocEntry
                                                         FROM          dbo.V_Poseican))
                            GROUP BY dbo.OINV.DocNum, dbo.OINV.DocEntry) AS Cobros ON dbo.OJDT.TransId = Cobros.id_cobro
WHERE     (YEAR(dbo.OJDT.RefDate) >= 2014) AND (YEAR(dbo.ORCT.DocDate) >= 2014)









Select week(fecha, 3), fecha, granja, granja_id, jaula, jaula_id, lote, lote_id, diametro_pienso, proveedor, stock_avg_ini, stock_count_ini, sum(cantidad)
from consumos, produccion_simulado
where fecha = date
  and site = granja
  and unitname = jaula
  and groupid = lote
group by  week(fecha, 3), fecha, granja, granja_id, jaula, jaula_id, lote, lote_id, diametro_pienso, proveedor, stock_avg_ini, stock_count_ini




Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
                                              ifnull((Select sum(cantidad)
                                                        from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                                                       where a.id = ma.almacen_id
                                                         and p.id   = ma.pienso_id
                                                         and p.diametro_pellet_id = tp.id
                                                         and ma.fecha <= '2014-12-03'
                                                         and pp.id = p.proveedor_id
                                                         and tamanio_pellets.id = tp.id
                                                    group by pp.nombre, tp.diametro),0) as stock_real, 
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > '2014-12-03'
                                                         and fecha >= '2014-12-08'
                                                         and fecha <= '2014-12-14'
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > '2014-12-03'
                                                         and p.fecha_descarga >= '2014-12-08'
                                                         and p.fecha_descarga <= '2014-12-14'
                                                         and p.estado <> 'Descargado'
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos
                                        from proveedores_pienso, tamanio_pellets
                                       where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                       order by proveedores_pienso.nombre, tamanio_pellets.diametro



Select * 
  from consumos
  where fecha >= '2014-12-08'
    and lote = '1469'

Select * 
from produccion_simulado
where date >= '2014-12-08'
and groupid = '1468'



select j.nombre, g.nombre, ifnull(ps.groupid,'-'), ifnull(ps.stock_count_ini,0), 
       ifnull(ps.stock_avg_ini,0), ifnull(ps.stock_bio_ini,0), ifnull(ps.cantidad_toma,0), ifnull(c.pienso,'-'), ifnull(c.diametro_pienso,'-'), ifnull(c.cantidad,0),
       ifnull(e.num_tomas, 0), ifnull(e.num_tomas, 0)
  from jaulas j left join produccion_simulado ps on j.nombre = ps.unitname and ps.date =  '2014-12-31' 
                left join consumos c on j.nombre = c.jaula and c.fecha =  '2014-12-31'
                left join estadillos e on j.id = e.jaula_id and e.fecha =  '2014-12-31',  granjas g
 where j.granja_id = 2
  and j.granja_id = g.id
   order by j.nombre


Select week(fecha, 3), fecha, produccion_simulado.granja, produccion_simulado.granja_id, 
       jaulas.nombre, jaulas.id, produccion_simulado.lote, produccion_simulado.lote_id, 
       diametro_pienso, proveedor, 
       stock_avg_ini, stock_count_ini, sum(cantidad)
  from jaulas left join consumos on jaulas.nombre = conumos.unitname, produccion_simulado
 where fecha = '2015-01-12'
   and granja_id = 1
   and fecha = date
   and site = granja
   and unitname = jaula
   and groupid = lote
group by  week(fecha, 3), fecha, granja, granja_id, jaula, jaula_id, lote, lote_id, diametro_pienso, proveedor, stock_avg_ini, stock_count_ini
order by jaula


*******************************************************************  Estadillos ***********************************************************
Select week(fecha, 3), month(fecha), fecha, granja, granja_id, jaula, jaula_id, lote, lote_id, diametro_pienso, proveedor, stock_avg_ini, stock_count_ini, sum(cantidad)
from consumos, produccion_simulado
where fecha >= '2015-04-01'
  and fecha = date
  and site = granja
  and unitname = jaula
  and groupid = lote
group by  week(fecha, 3), fecha, granja, granja_id, jaula, jaula_id, lote, lote_id, diametro_pienso, proveedor, stock_avg_ini, stock_count_ini


Select almacenes.nombre as almacen, piensos.id, piensos.nombre as pienso, 
                                      sum(cantidad) as cantidad, max(fecha)
                                          from movimientos_almacenes, almacenes, piensos
                                         where almacenes.id = movimientos_almacenes.almacen_id
                                           and piensos.id   = movimientos_almacenes.pienso_id
                                           and movimientos_almacenes.fecha <= '2015-03-09'
                                           and piensos.nombre = 'L-4 ALTERNA 2P'
                                           and almacenes.nombre = 'Martín e Hijos'
                                      group by almacenes.nombre, piensos.id, piensos.nombre order by almacenes.nombre, piensos.nombre desc


                                      SELECT * FROM estadillos WHERE fecha = '2015-03-19' order by id



Select proveedores_pienso.nombre, tamanio_pellets.diametro, 
                                              ifnull((Select sum(cantidad)
                                                        from movimientos_almacenes ma, almacenes a, piensos p , tamanio_pellets tp, proveedores_pienso pp
                                                       where a.id = ma.almacen_id
                                                         and p.id   = ma.pienso_id
                                                         and p.diametro_pellet_id = tp.id
                                                         and ma.fecha <= '2015-04-07'
                                                         and pp.id = p.proveedor_id
                                                         and tamanio_pellets.id = tp.id
                                                    group by pp.nombre, tp.diametro),0) as stock_real, 
                                              ifnull((Select sum(cantidad)
                                                        from piensos p , tamanio_pellets tp, proveedores_pienso pp, consumos c
                                                       where c.proveedor_id = pp.id
                                                         and c.pienso_id = p.id
                                                         and tp.id = p.diametro_pellet_id
                                                         and tamanio_pellets.id = tp.id
                                                         and fecha > '2015-04-07'
                                                         and fecha  >= '2015-04-06'
                                                         and fecha  <= '2015-04-12'
                                                    group by pp.nombre, tp.diametro),0) as consumo_simulado,
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga > '2015-04-07'
                                                         and p.fecha_descarga >= '2015-04-06'
                                                         and p.fecha_descarga <= '2015-04-12'
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos, 
                                              ifnull((Select sum(cantidad)
                                                        from pedidos_detalles pd, pedidos p, piensos ps, tamanio_pellets tp, proveedores_pienso pp
                                                       where tamanio_pellets.id = tp.id
                                                         and pd.pedido_id = p.id  
                                                         and pd.pienso_id = ps.id
                                                         and tp.id = ps.diametro_pellet_id
                                                         and ps.proveedor_id = pp.id
                                                         and p.fecha_descarga <= '2015-04-07'
                                                         and p.estado <> 'Descargado' 
                                                    group by pp.nombre, tp.diametro ) ,0) as pedidos_sin_descargar
                                        from proveedores_pienso, tamanio_pellets
                                       where proveedores_pienso.id = tamanio_pellets.proveedor_pienso_id
                                       order by proveedores_pienso.nombre, tamanio_pellets.diametro