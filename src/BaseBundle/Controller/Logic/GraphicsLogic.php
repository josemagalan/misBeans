<?php

namespace BaseBundle\Controller\Logic;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contenedor de las funciones necesarias para graficar.
 *
 * Class GraphicsLogic
 * @package BaseBundle\Controller\Logic
 */
class GraphicsLogic extends Controller
{

    /**
     * Genera de manera aleatoria un color en hexadecimal
     *
     * @return string
     */
    public function randomColor()
    {
        $rand = strtoupper(dechex(rand(0x000000, 0xFFFFFF)));
        return ('#' . $rand);
    }

    /**
     * Estadísticas globales modo quesito de reparto de $value.
     *
     * @param array $data
     * @param $value = alubiaBlanca || alubiaRoja || Fx(utilidad)
     * @return string
     */
    public function donutJsArray(array $data, $value)
    {
        $result = '';
        foreach ($data as $row) {
            $result .= '{value:' . $row[$value] . ',color:"' . $this->randomColor() .
                '",highlight:"' . $this->randomColor() . '",label: "' . $row['username'] . '"},';
        }
        return ($result);

    }

    /**
     * Evolución de alubias y función de utilidad a lo largo de la partida
     *
     * @param array $data
     * @return string
     */
    public function linesUserJsArray(array $data)
    {
        $result = ' labels: [';
        foreach ($data as $row) {
            $result .= '"' . $row['modificado']->format('H:i') . '", ';
        }
        $result .= '],
            datasets: [{
            label: "Alubias Blancas",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: [';
        foreach ($data as $row) {
            $result .= $row['aluBlanca'] . ', ';
        }
        $result .= '] }, {
            label: "Alubias rojas",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [';
        foreach ($data as $row) {
            $result .= $row['aluRoja'] . ', ';
        }
        $result .= '] }, {
            label: "funcion utilidad",
            fillColor: "rgba(50,100,150,0.2)",
            strokeColor: "rgba(50,100,150,1)",
            pointColor: "rgba(50,100,150,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(50,100,150,1)",
            data: [';
        foreach ($data as $row) {
            $result .= $row['fUtilidad'] . ', ';
        }
        $result .= '] }]';
        return $result;
    }

    /**
     * Evolucion del ratio de cambio a lo largo de la partida
     *
     * @param array $data
     * @return string
     */
    public function linesRatioJsArray(array $data)
    {
        $result = ' labels: [';
        foreach ($data as $row) {
            $result .= '"' . $row['modificado']->format('H:i') . '", ';
        }
        $result .= '],
            datasets: [{
            label: "Ratio alubias",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: [';
        foreach ($data as $row) {
            $result .= $row['ratio'] . ', ';
        }
        $result .= '] }]';
        return $result;
    }

    /**
     * Cantidad absoluta de alubias intercambiadas en las ofertas de la partida
     *
     * @param array $data
     * @return string
     */
    public function barBeansJsArray(array $data)
    {
        $result = ' labels: [';
        foreach ($data as $row) {
            $result .= '"' . $row['modificado']->format('H:i') . '", ';
        }
        $result .= '],
            datasets: [
            {
                label: "Alubias rojas",
                fillColor : "rgba(151,187,205,0.5)",
				strokeColor : "rgba(151,187,205,0.8)",
				highlightFill : "rgba(151,187,205,0.75)",
				highlightStroke : "rgba(151,187,205,1)",
                data: [';
        foreach ($data as $row) {
            $result .= $row['rojas'] . ', ';
        }
        $result .= '] },
            {   label: "Alubias blancas",
                fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,0.8)",
				highlightFill: "rgba(220,220,220,0.75)",
				highlightStroke: "rgba(220,220,220,1)",
                data: [';
        foreach ($data as $row) {
            $result .= $row['blancas'] . ', ';
        }
        $result .= '] }]';
        return $result;
    }
}