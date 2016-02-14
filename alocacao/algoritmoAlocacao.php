<!DOCTYPE html>
<html>
<head>
	<title>Resultado do Quadro de Alocação</title>
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
			<h1 style="text-align: center; font-family: 'Montserrat', sans-serif;"> Resultado do Quadro de Alocação</h2>
			
			<?php
				session_start('criaArquivo');
				$M;
				$tamTotalLinhas = 1;
				$tamTotalColunas = 1;
				function insereMatriz($linha){
					global $M, $tamTotalLinhas, $tamTotalColunas;
					$linhaDiv = explode(" ", $linha);
					$tamTotalColunas = 1;
					foreach($linhaDiv as $value){
						$M[$tamTotalLinhas][$tamTotalColunas] = $value;
						$tamTotalColunas++;
					}
				}
				function subtraiMenorLinha($percLinha,$menor){
					global $M, $tamTotalLinhas, $tamTotalColunas;
					for($percColuna=0;$percColuna<($tamTotalColunas-1);$percColuna++){
						$M[$percLinha][$percColuna] = $M[$percLinha][$percColuna]-$menor;
					}
				}
				function subtraiMenorColuna($percLinha,$menor){
					global $M, $tamTotalLinhas, $tamTotalColunas;
					for($percLinha=0;$percLinha<$tamTotalLinhas;$percLinha++){
						$M[$percLinha][$percColuna] = $M[$percLinha][$percColuna]-$menor;
					}
				}
				function metodoHungaro(){
					global $M, $tamTotalLinhas, $tamTotalColunas;
					
					//encontra o menor de cada linha
					for($percLinha=0;$percLinha<$tamTotalLinhas;$percLinha++){
						$menor = $M[$percLinha][0];
						for($percColuna=0;$percColuna<($tamTotalColunas-1);$percColuna++){
							if($M[$percLinha][$percColuna]<$menor)
								$menor = $M[$percLinha][$percColuna];
						}
						//subtrai o menor de cada linha
						function subtraiMenorLinha($percLinha,$menor);
					}
					//encontra o menor de cada coluna
					for($percColuna=0;$percColuna<($tamTotalColunas-1);$percColuna++){
						$menor = $M[0][$percColuna];
						for($percLinha=0;$percLinha<$tamTotalLinhas;$percLinha++){
							if($M[$percLinha][$percColuna]<$menor)
								$menor = $M[$percLinha][$percColuna];
						}
						//subtrai o menor de cada coluna
						function subtraiMenorColuna($percLinha,$menor);
					}
				}
				$arquivo = fopen('upload/' . $_SESSION['nomeArquivoSimplex'],"r");
				while (!feof($arquivo)){
					$linha = fgets($arquivo,4096);
					insereMatriz($linha);
					$tamTotalLinhas++;
				}
				for($percLinha=0;$percLinha<$tamTotalLinhas;$percLinha++)
					$M[$percLinha][0] = -1;
				for($percColuna=0;$percColuna<($tamTotalColunas-1);$percColuna++)
					$M[0][$percColuna] = -1;
				
				metodoHungaro();

				print_r($M);
				fclose ($arquivo);
				unlink('upload/' . $_SESSION['nomeArquivoSimplex']);
				unset($_SESSION); 
				session_destroy(); 
			?>
		</div>
		
	</div>
</body>
</html>