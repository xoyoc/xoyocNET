	</main>
 	<footer id="contacto" class="pie-pagina twelve.columns">
		<form class="pie-pagina__formulario" action="">
		<label class="pie-pagina__formulario--contenedor centrado-flex">
            	<input name="nombre" id="c_name" type="text" class="pie-pagina__formulario--campo-texto" placeholder="Nombre..."/>
            	<input name="email"  id="c_mail" type="email" class="pie-pagina__formulario--campo-texto" placeholder="Email..."/>
            	<textarea name="mensaje"  id="c_msg" class="pie-pagina__formulario--area-texto" placeholder="Mensaje..."></textarea>
            	<input name="send" onclick="cargaSendMail()" type="button" value="Enviar" class="btn-b" id="c_enviar"></input>
		</label>
		</form>
	</footer>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="<?php bloginfo("template_url"); ?>/js/menu.js"></script>
</body>
</html>