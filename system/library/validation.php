<?php 
class Validation {
	
	function __construct ( $valor = null ) {
		$this->valor = preg_replace( '/[^0-9]/', '', $valor );
		$this->valor = (string)$this->valor;
	}

	protected function verifica_cpf_cnpj () {
		if ( strlen( $this->valor ) === 11 ) {
			return 'CPF';
		} elseif ( strlen( $this->valor ) === 14 ) {
			return 'CNPJ';
		} else {
			return false;
		}
	}
	
	protected function calc_digitos_posicoes( $digitos, $posicoes = 10, $soma_digitos = 0 ) {
		for ( $i = 0; $i < strlen( $digitos ); $i++  ) {
			$soma_digitos = $soma_digitos + ( $digitos[$i] * $posicoes );
			$posicoes--;
			if ( $posicoes < 2 ) {
				// Retorno a posição para 9
				$posicoes = 9;
			}
		}

		$soma_digitos = $soma_digitos % 11;

		if ( $soma_digitos < 2 ) {
			$soma_digitos = 0;
		} else {
			$soma_digitos = 11 - $soma_digitos;
		}
		$cpf = $digitos . $soma_digitos;

		if ($cpf == "11111111111") {
		return false;
		} elseif ($cpf == "22222222222"){
		return false;
		} elseif ($cpf == "33333333333"){
		return false;
		} elseif ($cpf == "44444444444"){
		return false;
		} elseif ($cpf == "55555555555"){
		return false;
		} elseif ($cpf == "66666666666"){
		return false;
		} elseif ($cpf == "77777777777"){
		return false;
		} elseif ($cpf == "88888888888"){
		return false;
		} elseif ($cpf == "99999999999"){
		return false;
		} elseif ($cpf == "00000000000"){
		return false;
		} else {
		return $cpf;
		}

	}

	protected function validar_cpf() {
		$digitos = substr($this->valor, 0, 9);
		$novo_cpf = $this->calc_digitos_posicoes( $digitos );
		$novo_cpf = $this->calc_digitos_posicoes( $novo_cpf, 11 );
		if ( $novo_cpf === $this->valor ) {
			return true;
		} else {
			return false;
		}
	}

	protected function validar_cnpj () {
		$cnpj_original = $this->valor;
		$primeiros_numeros_cnpj = substr( $this->valor, 0, 12 );
		$primeiro_calculo = $this->calc_digitos_posicoes( $primeiros_numeros_cnpj, 5 );
		$segundo_calculo = $this->calc_digitos_posicoes( $primeiro_calculo, 6 );
		$cnpj = $segundo_calculo;
		if ( $cnpj === $cnpj_original ) {
			return true;
		}
	}
	
	public function cpf() {
		if ( $this->verifica_cpf_cnpj() === 'CPF' ) {
			return $this->validar_cpf();
		} else {
			return false;
		}
	}

	public function cnpj() {
        if ( $this->verifica_cpf_cnpj() === 'CNPJ' ) {
			return $this->validar_cnpj();
		} else {
			return false;
		}
	}
	
	public function ambos() {
		if ( $this->verifica_cpf_cnpj() === 'CPF' ) {
			return $this->validar_cpf();
		} elseif ( $this->verifica_cpf_cnpj() === 'CNPJ' ) {
            return $this->validar_cnpj();
		} else {
			return false;
		}
	}
}
