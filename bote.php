<?php
/*
* Plugin Name: Bote ShortCode
* Description: Crea una tabla con los movimentos del bote
* Version: 1.0
* Author: 15hack
* Author URI: http://15hack.tomalaplaza.net/bote
*/

function bote_creation($atts, $content=null){
	global $wpdb;
	$count = $wpdb->get_var("select count(*) from cuenta_triodos where valor is not null");
	if ($count == 0) {
		return "<p>No hay movimientos en la cuenta.</p>";
	}
	$row = $wpdb->get_row("select sum(valor) saldo, max(fecha) ult from cuenta_triodos where valor is not null");
	$html=
	'<p>
		<strong>Última actualización:</strong> ' . date("d-m-Y", strtotime($row->ult)) . ' (*)<br/>
		<strong>Saldo:</strong> ' . number_format ($row->saldo , 2 , "," , "," ) . ' €';
    $saldo = $row->saldo;
	$row = $wpdb->get_row("select CEIL(ABS(sum(valor) / CEIL(DATEDIFF(max(fecha),min(FECHA))/30))) media, MIN(fecha) desde, CEIL(DATEDIFF(current_date,min(FECHA)) /30) meses from cuenta_triodos where valor is not null and valor<0 and DATEDIFF(current_date, fecha)<=30*14");
    if ($row->meses>2) {
        $queda = floor($saldo / $row->media);
        $html= $html . ' <span title="En base a los costes calculados sobre los últimos ' . $row->meses . ' meses">(da para ' . $queda . ' meses más)</span>';
    }
	$html= $html . '
	</p>
	<table>
		<thead>
			<tr>
				<th>Fecha</th>
				<th>Concepto</th>
				<th style="text-align: right;">Cantidad</th>
			</tr>
		</thead>
		<tbody>';
	$res=$wpdb->get_results("select fecha,concepto,valor from cuenta_triodos where valor is not null order by fecha DESC");
	foreach ( $res as $re ) {
		$color = ($re->valor<0? 'red': 'blue');
		$html= $html . '
		<tr>
			<td>' . date("d-m-Y", strtotime($re->fecha)) . '</td>
			<td style="color: ' . $color . ';">' . (is_null($re->concepto)?'(*)':$re->concepto) . '</td>
			<td style="text-align: right; color: ' . $color . ';">' . number_format ( $re->valor , 2 , "," , "," ) .' €</td>
		</tr>';
	}
	return $html . '
		</tbody>
	</table>
	<p>(*) Esta tabla se actualiza semi-automáticamente, pudiendo tardar varios días en mostrar los últimos datos, en especial los conceptos de los movimientos.</p>
        <p>Si quieres que tu donación quede registrada a nombre de algún colectivo indicalo en el concepto de la transferencia o mandanos un correo a <a href="mailto:15hack@riseup.net">15hack@riseup.net</a> notificándonoslo.</p>';
}
add_shortcode('bote', 'bote_creation');
?>
