<!DOCTYPE html>
<html>
<head>
	<title>vSimplex</title>
	<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	<style type="text/css">
		body{
			margin: 0;
			background-color: #4A98E5;
			background-image: url("images/overlay.png"), url("images/pattern-size3.svg");
		}
		#container{
			width: 850px;
			height: 600px;
			background-image: url("images/res_po.png");
			background-repeat: no-repeat;
			margin: 10px auto;
		}
		#resultado{
			width: 799px;
			height: 451px;
			background-color: white;
			position: relative;
			top:31px;
			left: 25px;
			border-radius: 5px;

		}
	</style>
	<script type="text/javascript">



	</script>
</head>
<body>
	
	<div id="container">
		<div id="resultado">
			<br>
			<h1 style="text-align: center; font-family: 'Montserrat', sans-serif;"> Resultado do Simplex (Com Cortes) - Quadro Final</h2>
			
			<?php
				session_start('criaArquivo');
				$M;
				$folgas;
				$vetorB;
				$colunaLinhaUm = 0;
				$countVetorB = 0;
				$countFolgas = 0;
				$funcaoObjetivo;
				$i = 0;
				$linhaAtualMatriz = 0;
				//Tamanho total de linhas e colunas;
				$tamTotalColunas = 0;
				$tamTotalLinhas = 0;
				// Insere valores na Matriz, mas não adiciona as folgas e o vetor b
				function insereMatriz($linha){
					global $M, $i, $funcaoObjetivo, $linhaAtualMatriz, $folgas, $countFolgas, $vetorB, $countVetorB, $colunaLinhaUm;
					$flagDesigualdade = 0;
					$countDiv = 0;
					$opAnterior = 1;
					if($i==0){
						$coluna = 0;
						$linhaDiv = explode(" ",$linha);
						foreach ($linhaDiv as $value) {
							if($countDiv==0){
								$funcaoObjetivo = $value;
							}
							else{
								if(strcmp("Z",$value)==0){
									$M[0][0] = "Z";
									$M[1][0] = 1;
									$coluna++;
									$colunaLinhaUm++;
									continue;
								}
								else if(strcmp("=",$value)==0)
									continue;
								else if(strcmp("+",$value)==0){
									$opAnterior = 1;
								}
								else if(strcmp("-",$value)==0){
									$opAnterior = -1;
								}
								else{
									$campoDiv = explode(".",$value);
									$M[1][$coluna] = $campoDiv[0]*$opAnterior;
									$M[0][$coluna] = $campoDiv[1];
									$colunaLinhaUm++;
									$coluna++;
								}
							}
							$countDiv++;
						}
						$linhaAtualMatriz = 2;
					}
					else if($i>1){
						$coluna = 1;
						$linhaDiv = explode(" ",$linha);
						foreach ($linhaDiv as $value) {
							if($flagDesigualdade==1){
								$vetorB[$countVetorB] = $value;
								$countVetorB++;
							}
							if($value=="+"){
								$opAnterior = 1;
							}
							else if($value=="-"){
								$opAnterior = -1;
							}
							else if(strcmp("<=",$value)==0){
								$folgas[$countFolgas] = 1;
								$countFolgas++;
								$flagDesigualdade = 1;
							}
							else if(strcmp(">=",$value)==0){
								$folgas[$countFolgas] = -1;
								$countFolgas++;
								$flagDesigualdade = 1;
							}
							else if(strcmp("<",$value)==0){
								$folgas[$countFolgas] = 1;
								$countFolgas++;
								$flagDesigualdade = 1;
							}
							else if(strcmp(">",$value)==0){
								$folgas[$countFolgas] = -1;
								$countFolgas++;
								$flagDesigualdade = 1;
							}
							else{
								if($flagDesigualdade==0){
									$campoDiv = explode(".",$value);
									$M[$linhaAtualMatriz][0] = 0;
									$M[$linhaAtualMatriz][$coluna] = $campoDiv[0] * $opAnterior;
									$coluna++;
									$opAnterior = 1;
								}
							}
							
						}
						$linhaAtualMatriz++;
					}
				}
				// Insere as folgas e o vetor b
				function formaPadraoFinal(){
					global $M, $i, $funcaoObjetivo, $linhaAtualMatriz, $folgas, $countFolgas, $vetorB, $countVetorB, $colunaLinhaUm;
					global $tamTotalColunas, $tamTotalLinhas;
					$numeroFolgas = 1;
					foreach ($folgas as $value){
						$M[0][$colunaLinhaUm] = "XF" . $numeroFolgas;
						$colunaLinhaUm++;
						$numeroFolgas++;
					}
					$M[0][$colunaLinhaUm] = "B";
					$colunaLinhaUm++;
					$p = 0;
					while(strcmp($M[0][$p],"B")!=0){
						$p++;
						$tamTotalColunas++;
					}
					$tamTotalColunas++; 
					// Adiciona os zeros nas colunas e o valor dos coeficientes das folgas
					$somaColunaFolgas = $tamTotalColunas - $numeroFolgas;
					$posicaoFolgas = 0;
					for($percLinha=2;$percLinha<$tamTotalLinhas;$percLinha++){
						$tmp = $tamTotalColunas - $numeroFolgas; 
						for($percColuna=$tmp;$percColuna<$tamTotalColunas;$percColuna++){
							if($percColuna==$somaColunaFolgas)
								$M[$percLinha][$percColuna] = $folgas[$posicaoFolgas];
							else
								$M[$percLinha][$percColuna] = 0;
						}
						$posicaoFolgas++;
						$somaColunaFolgas++;
					}
					// Adiciona 0s nas colunas de folga da linha 1 (Função Objetivo)
					for($percColuna = $tamTotalColunas - $numeroFolgas;$percColuna<$tamTotalColunas;$percColuna++)
						$M[1][$percColuna] = 0;

					// O vetor b
					$verificaPosicaob = 0;
					for($percLinha=2;$percLinha<$tamTotalLinhas;$percLinha++){
						$M[$percLinha][$tamTotalColunas-1] = $vetorB[$verificaPosicaob];
						$verificaPosicaob++;
					}
					
				}
				function metodoCortesGomory(){
					global $M, $tamTotalLinhas, $tamTotalColunas, $numeroFolgas;
					// Busca Variáveis Básicas
					$flagInteiro = 1;	//0 para float
					$vetorCoeficientes;
					$vetorVariaveis;
					for ($percColuna=1;$percColuna<($tamTotalColunas-1);$percColuna++){
						$flagNaoIdentidade = 0; 
						for($percLinha=1;$percLinha<$tamTotalLinhas;$percLinha++){
							if($M[$percLinha][$percColuna]==(int)0 or $M[$percLinha][$percColuna]==(int)1){
								$flagNaoIdentidade = 0; 
								$colunaIdentidade = $percColuna;
							}
							else{
								$colunaIdentidade = $percColuna;
								$flagNaoIdentidade = 1;
								break;
								
							}
						}
						if($flagNaoIdentidade==0){
							for($percLinha=1;$percLinha<$tamTotalLinhas;$percLinha++){
								if($M[$percLinha][$colunaIdentidade]==1)
									$linhaIdentidade = $percLinha;
							}
							if(is_float($M[$linhaIdentidade][$tamTotalColunas-1])){
								$flagInteiro = 0;
								break;
							}
						}
					}
					if($flagInteiro==1){
						return 1;
					}
					else{	//Cria linha Matriz de restrição
						$tamVetorCoeficienteVar = 0;
						for($percColuna=1;$percColuna<$tamTotalColunas;$percColuna++){
							$vetorCoeficientes[$tamVetorCoeficienteVar] = $M[$linhaIdentidade][$percColuna];
							$vetorVariaveis[$tamVetorCoeficienteVar] = $M[0][$percColuna]; //0 pois pega a variável
							$tamVetorCoeficienteVar++;
						}
						$vetorCoeficientes[$tamanhoCoeficienteVar-1] = $vetorCoeficientes[$tamanhoCoeficienteVar-1] - floor($vetorCoeficientes[$tamanhoCoeficienteVar-1]);
						for($percVetor=0;$percVetor<($tamanhoCoeficienteVar-1);$percVetor++){
							$vetorCoeficientes[$percVetor] = $vetorCoeficientes[$percVetor]*(-1);
						}
						//Adiciona restrição na matriz
						$percVetor = 0;
						for($percColuna=0;$percColuna<$tamTotalColunas;$percColuna++){
							if($percColuna==0){
								$M[$tamTotalLinhas][$percColuna] = 0;
							}
							else if($percColuna==($tamTotalColunas-1)){
								for($percLinha=0;$percLinha<=$tamTotalLinhas;$percLinha++){
									if($percLinha==0){
										$M[$percLinha][$percColuna+1] = $M[$percLinha][$percColuna];
										$M[$percLinha][$percColuna] = "XF" . $numeroFolgas;
										$numeroFolgas++;
									}
									else{
										$M[$percLinha][$percColuna+1] = $M[$percLinha][$percColuna];
										$M[$percLinha][$percColuna] = 0;
									}
								}
							}
							else{
								$M[$tamTotalLinhas][$percColuna] = $vetorCoeficientes[$percVetor];
								$percVetor++;
							}
						}
						$tamTotalLinhas++;
						$tamTotalColunas++;
						$M[$tamTotalLinhas-1][$tamTotalColunas-2] = 1;
						return 0;
						// Após isso, ele executa o simplex novamente!
					}
				}
				//Algoritmo Simplex
				function algoritmoSimplex(){
					global $M, $tamTotalLinhas, $tamTotalColunas, $funcaoObjetivo;
					//Arrumando o quadro para a função min ou max
					$numeroIteracoes = 0;
					if(strcmp($funcaoObjetivo,"MIN")==0){
						$M[1][0] = $M[1][0]*(-1);
					}
					else{
						for($p=1;$p<($tamTotalColunas-2);$p++){
							$M[1][$p] = $M[1][$p]*(-1);
						}
					}
					$statusSimplex = 0;
					while($statusSimplex!=1){
						// Encontra a variável que entra, ou seja, o menor valor
						$numeroIteracoes++;
						for($percColuna=1;$percColuna<($tamTotalColunas-1);$percColuna++){
							if($percColuna==1){
								$menorValor = $M[1][$percColuna];
								$variavelEntrada = $percColuna;
							}
							else{
								if(($M[1][$percColuna])<$menorValor){
									$menorValor = $M[1][$percColuna];
									$variavelEntrada = $percColuna;
								}
							}
						}
						if($menorValor>=0){
							return;				
						}
						//Encontra a linha pivô
						$linhaPivo = -1;
						for ($percLinha=2;$percLinha<$tamTotalLinhas;$percLinha++){ 
							if($linhaPivo==-1 && (($M[$percLinha][$tamTotalColunas-1])/($M[$percLinha][$variavelEntrada])>=0)){
								$menorDivisao = ($M[$percLinha][$tamTotalColunas-1])/($M[$percLinha][$variavelEntrada]);
								$linhaPivo = $percLinha;
							}
							else if(($M[$percLinha][$tamTotalColunas-1])/($M[$percLinha][$variavelEntrada])>0){
								if(($M[$percLinha][$tamTotalColunas-1])/($M[$percLinha][$variavelEntrada])<$menorDivisao){
									$menorDivisao = ($M[$percLinha][$tamTotalColunas-1])/($M[$percLinha][$variavelEntrada]);
									$linhaPivo = $percLinha;
								}
							}
						}
						$elementoPivo = $M[$linhaPivo][$variavelEntrada];
						// Calcula a nova linha pivô
						for($percColuna=0;$percColuna<$tamTotalColunas;$percColuna++){
							$M[$linhaPivo][$percColuna] = ($M[$linhaPivo][$percColuna])/$elementoPivo;
						}

						// Calcula novas linhas
						for($percLinha=1;$percLinha<$tamTotalLinhas;$percLinha++){
							$elementoInv = ($M[$percLinha][$variavelEntrada])*(-1);
							if($percLinha!=$linhaPivo){
								for($percColuna=0;$percColuna<$tamTotalColunas;$percColuna++){
									$M[$percLinha][$percColuna] = $M[$percLinha][$percColuna] + $M[$linhaPivo][$percColuna]*$elementoInv;
								}
							}
						}
						if($numeroIteracoes==15)
							return;
						$statusSimplex = metodoCortesGomory();
					}
				}
				function imprimirTabelaFinalSimplex(){
					global $M, $tamTotalLinhas, $tamTotalColunas;
					echo "<br><br><br>";
					echo "<center>";
					echo "<table border='1' class='table table-hover' cellspacing=0 width=600px>";
					for($percLinha=0;$percLinha<$tamTotalLinhas;$percLinha++){
						echo "<tr>";
						for($percColuna=0;$percColuna<$tamTotalColunas;$percColuna++){
							if($percLinha!=0)
								echo "<td>" . number_format($M[$percLinha][$percColuna],2) . "</td>";
							else
								echo "<th>" . $M[$percLinha][$percColuna] . "</th>";
						}
						echo "</tr>";
					}
					echo "</table>";
					echo "</center>";

				}
				//number_format($number, 2, ',', ' ');

				$arquivo = fopen('upload/' . $_SESSION['nomeArquivoSimplex'],"r");
				while (!feof($arquivo)){
					$linha = fgets($arquivo,4096);
					insereMatriz($linha);
					$i++;
					$tamTotalLinhas++;
				}
				formaPadraoFinal();
				algoritmoSimplex();
				imprimirTabelaFinalSimplex();

				fclose ($arquivo);
				unlink('upload/' . $_SESSION['nomeArquivoSimplex']);
				unset($_SESSION); 
				session_destroy(); 
			?>
		</div>
		
	</div>
</body>
</html>