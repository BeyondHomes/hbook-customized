'use strict';

function DocumentTmpl( brand_new, id, name, content, lang ) {
	HbSetting.call( this, brand_new, 'document_template', id, name );

	this.content = ko.observable( content );
	this.lang = ko.observable( lang );

	var self = this;

	this.content_html = ko.computed( function() {
		var content = self.content(),
			long_content = false;
		if ( content.length > 100 ) {
			long_content = true;
			content = content.substr( 0, 100 );
		}
		content = content.replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
		if ( long_content ) {
			content = content + '<b>...</b>';
		}
		content = content.replace( /(?:\r\n|\r|\n)/g, '<br/>' );
		return content;
	});

	this.lang_text = ko.computed( function() {
		for ( var i = 0; i < hb_doc_langs.length; i++ ) {
			if ( hb_doc_langs[i]['lang_value'] == self.lang() ) {
				return hb_doc_langs[i]['lang_name'];
			}
		}
	});

	this.revert = function( document_tmpl ) {
		if ( document_tmpl ) {
			self.name( document_tmpl.name );
			self.content( document_tmpl.content );
			self.lang( document_tmpl.lang );
		}
	}
}

function DocumentTmplViewModel() {
	var self = this;

	var observable_document_tmpls = [];
	for ( var i = 0; i < document_tmpls.length; i++ ) {
		observable_document_tmpls.push(
			new DocumentTmpl(
				false,
				document_tmpls[i].id,
				document_tmpls[i].name,
				document_tmpls[i].content,
				document_tmpls[i].lang
			)
		);
	}

	this.document_tmpls = ko.observableArray( observable_document_tmpls );

	ko.utils.extend( this, new HbSettings() );

	this.create_document_tmpl = function() {
		var new_document_tmpl = new DocumentTmpl( true, 0, hb_text.new_document_tmpl, '', '', '' );
		self.create_setting( new_document_tmpl, function( new_document_tmpl ) {
			self.document_tmpls.push( new_document_tmpl );
		});
	}

	this.remove = function( setting ) {
		var callback_function = function() {
			self.document_tmpls.remove( setting );
		}
		self.delete_setting( setting, callback_function );
	}
}

ko.applyBindings( new DocumentTmplViewModel() );