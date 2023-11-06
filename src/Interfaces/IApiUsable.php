<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface IApiUsable
{
	//public function TraerUno($request, $response, $args);
	public function TraerTodos(Request $request, Response $response,array $args);
	public function CargarUno(Request $request,Response $response,array $args);
	//public function BorrarUno($request, $response, $args);
	//public function ModificarUno($request, $response, $args);
}