( function() {

	var bind = function( fn, me ){
		return function(){
			return fn.apply( me, arguments ); 
		};
	};
	
	jQuery( document ).ready( function( $ ) {
		
		'use strict';
		
		return window.Woodev_Job_Batch_Handler = ( function() {
			
			function Woodev_Job_Batch_Handler( args ) {
				
				this.process_job = bind(this.process_job, this);
				this.id = args.id;
				this.process_nonce = args.process_nonce;
				this.cancel_nonce = args.cancel_nonce;
				this.cancelled = false;
			}
			
			Woodev_Job_Batch_Handler.prototype.process_job = function( job_id ) {
				
				return new Promise( ( function( _this ) {
					
					return function( resolve, reject ) {
						
						var data;
						
						if ( _this.cancelled === job_id ) {
							return _this.cancel_job( job_id );
						}
						
						data = {
							action: _this.id + "_process_batch",
							security: _this.process_nonce,
							job_id: job_id
						};
						
						return $.post( ajaxurl, data ).done( function( response ) {
							
							if ( ! ( response.success && ( response.data != null ) ) ) {
								return reject( response );
							}
							
							if ( response.data.status !== 'processing' ) {
								return resolve( response );
							}
							
							$( document ).trigger( _this.id + "_batch_progress_" + response.data.id, {
								percentage: response.data.percentage,
								progress: response.data.progress,
								total: response.data.total
							} );
							
							return resolve( _this.process_job( response.data.id ) );
						
						} ).fail( function( jqXHR, textStatus, error ) {
							return reject( error );
						} );
					
					};
				} )( this ) );
			};
			
			Woodev_Job_Batch_Handler.prototype.cancel_job = function( job_id ) {
				
				return new Promise( ( function( _this ) {
					
					return function( resolve, reject ) {
						
						var data;
						
						_this.cancelled = false;
						
						data = {
							action: _this.id + "_cancel_job",
							security: _this.cancel_nonce,
							job_id: job_id
						};
						
						return $.post( ajaxurl, data ).done( function( response ) {
							
							if ( ! response.success ) {
								return reject( response );
							}
							
							return resolve( response );
							
						} ).fail( function( jqXHR, textStatus, error ) {
							return reject( error );
						} );
					};
				} )( this ) );
			};
			
			return Woodev_Job_Batch_Handler;
			
		} )();
	
	} );

} ).call( this );
