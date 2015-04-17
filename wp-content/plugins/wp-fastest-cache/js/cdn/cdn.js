var WpfcCDN = {
	id : "",
	template_url : "",
	content : "",
	conditions : "",
	set_id: function(obj){
		this.id = obj.id;
	},
	set_template_url: function(obj){
		this.template_url = obj.template_main_url + "/" + this.id + ".html";
	},
	open_wizard: function(){
		var self = this;
		self.load_template(function(){
			self.set_buttons_action();
		});
	},
	set_buttons_action: function(){
		var self = this;
		var action = "";
		var current_page, next_page, current_page_number;

		jQuery("button[wpfc-cdn-modal-button]").click(function(e){
			action = jQuery(e.target).attr("wpfc-cdn-modal-button");
			current_page = jQuery("#wpfc-wizard-maxcdn div.wiz-cont:visible");
			current_page_number = jQuery("#wpfc-wizard-maxcdn div.wiz-cont:visible").attr("wpfc-cdn-page");
			next_page = current_page.next();
			prev_page = current_page.prev();

			if(action == "next"){
				if(next_page.length){
					if(self.conditions("next", current_page_number)){
						current_page.hide();
						current_page.next().show();
						self.show_button("back");
					}
				}
			}else if(action == "back"){
				if(prev_page.length){
					if(self.conditions("back", current_page_number)){
						current_page.hide();
						current_page.prev().show();
						self.show_button("next");

						if(prev_page.attr("wpfc-cdn-page") == 1){
							self.hide_button("back");
						}
					}
				}
			}
		});
	},
	hide_button: function(type){
		jQuery("button[wpfc-cdn-modal-button='" + type + "']").hide();
	},
	show_button: function(type){
		jQuery("button[wpfc-cdn-modal-button='" + type + "']").show();
	},
	load_template: function(callbak){
		var self = this;
		jQuery.get(self.template_url, function( data ) {
			jQuery("body").append(data);
			Wpfc_Dialog.dialog("wpfc-modal-" + self.id);
			callbak();
		});
	}
};