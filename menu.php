<?php 
define ( 'PATH', '/marretada/' );
?>

<html>
<style type="text/css">
<!--

-->
</style>
<head>

<style type="text/css">

ul.menubar{
  margin: 0px;
  padding: 0px;
  background-color: #FFFFFF; /* IE6 Bug */
  font-size: 100%;
}
 
ul.menubar .submenu{
  margin: 0px;
  padding: 0px;
  list-style:none;
  background-image:url(<?=PATH?>imagem/Fundo_box2.jpg);
  border: 1px solid #ccc;
  float:left;
}
 
ul.menubar ul.menu{
  display: none;
  position: absolute;
  margin: 0px;
}
 
ul.menubar a{
	padding: 5px;
	display:block;
	text-decoration:none;
	color: #FFFFFF;
	padding: 5px;
}
 
ul.menu, ul.menu ul{
  margin:0;
  padding:0;
  border-bottom: 5px solid #ccc;
  background-image:url(<?=PATH?>imagem/Fundo_boxe2.jpg);
  width: 294px; /* Width of Menu Items */
  background-color:#FFFFFF; /* IE6 Bug */
}
 
ul.menu li{
  position: relative;
  list-style: none;
  border: 0px;
}
 
ul.menu li a{
  display: block;
  text-decoration: none;
  border: 1px solid #ccc;
  border-bottom: 0px;
  color: #777;
  padding: 5px 10px 5px 5px;
}
 
ul.menu li sup{
  font-weight:bold;
  font-size:7px;
  color: red;
}
 
/* Fix IE. Hide from IE Mac \*/
* html ul.menu li { float: left; height: 1%; }
* html ul.menu li a { height: 1%; }
/* End */
 
ul.menu ul{
  position: absolute;
  display: none;
  left: 149px; /* Set 1px less than menu width */
  top: 0px;
}
 
ul.menu li.submenu ul { display:none; } /* Hide sub-menus initially */
 
ul.menu li a:hover { color: #E2144A; }

</style>
 
<script type="text/javascript">
function horizontal() {
 
   var navItems = document.getElementById("menu_dropdown").getElementsByTagName("li");
    
   for (var i=0; i< navItems.length; i++) {
      if(navItems[i].className == "submenu")
      {
         if(navItems[i].getElementsByTagName('ul')[0] != null)
         {
            navItems[i].onmouseover=function() {this.getElementsByTagName('ul')[0].style.display="block";this.style.backgroundColor = "#f9f9f9";}
            navItems[i].onmouseout=function() {this.getElementsByTagName('ul')[0].style.display="none";this.style.backgroundColor = "#FFFFFF";}
         }
      }
   }
 
}
 
</script>
 
</head>
 
<body onLoad="horizontal();">

<div id="pagina">
				<div id="topo_gif">
   				    <div id="logo">
        			
    			</div>
     			<div id="slogan">
            		    
   	  			</div>
       			 
      		</div>     
             <div id="menu">
            	
     		</div>
                    <ul id="menu_dropdown" class="menubar">
                       
                       <li class="submenu"><a href="#">Sped</a>
                          <ul class="menu">
                           	<li><a href="<?=PATH;?>sped-lista.php">Editar Movimentos</a></li>
                           	<li><a href="<?=PATH;?>sped-excluir-fiscal.php">Excluir Lan&ccedil;amentos Fiscais</a></li>
                          </ul>
                       </li>
					  
					  <li class="submenu"><a href="#">Lote</a>
                          <ul class="menu">
                           	<li><a href="<?=PATH;?>lista-partidas.php">Editar partidas</a></li>    	
                          </ul>
                       </li>  
                       
                       <li class="submenu"><a href="#">Faturamento</a>
                          <ul class="menu">
                           	<li><a href="<?=PATH;?>faturamento-ambiente.php">Mudan&ccedil;a de Ambiente</a></li>    	
                          </ul>
                       </li>
                       
                    </ul>
                 <br/>
             	 <br/>
	</div>       
</body>
</html>