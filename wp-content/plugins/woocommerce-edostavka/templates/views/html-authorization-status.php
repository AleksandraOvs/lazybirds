<style>
	@keyframes status-indicator-pulse {
		0%   { box-shadow: 0 0 0 0 rgba(216, 226, 233, .5); }
		70%  { box-shadow: 0 0 0 5px rgba(216, 226, 233, 0); }
		100% { box-shadow: 0 0 0 0 rgba(216, 226, 233, 0); }
	}
	@keyframes status-indicator-pulse-positive {
		0%   { box-shadow: 0 0 0 0 rgba(75, 210, 143, .5); }
		70%  { box-shadow: 0 0 0 5px rgba(75, 210, 143, 0); }
		100% { box-shadow: 0 0 0 0 rgba(75, 210, 143, 0); }
	}
	@keyframes status-indicator-pulse-negative {
		0%   { box-shadow: 0 0 0 0 rgba(255, 77, 77, .5); }
		70%  { box-shadow: 0 0 0 5px rgba(255, 77, 77, 0); }
		100% { box-shadow: 0 0 0 0 rgba(255, 77, 77, 0); }
	}
	.status-indicator {
		display: inline-block;
		border-radius: 50%;
		width: 7px;
		height: 7px;
		animation-name: status-indicator-pulse;
		animation-duration: 2s;
		animation-timing-function: ease-in-out;
		animation-iteration-count: infinite;
		animation-direction: normal;
		animation-delay: 0;
		animation-fill-mode: none;
	}

	.status-indicator__positive {
		background-color: rgb(75, 210, 143);
		animation-name: status-indicator-pulse-positive;
	}

	.status-indicator__negative {
		background-color: rgb(255, 77, 77);
		animation-name: status-indicator-pulse-negative;
	}
</style>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label>Статус подключения: <?php echo wc_help_tip( 'Текущий статус подключения к API СДЭК.' );?></label>
	</th>
	<td class="forminp">
		<?php if( $status ) : ?>
			<p><span class="status-indicator status-indicator__positive"></span> Аккаунт подключён</p>
		<?php else : ?>
			<p><span class="status-indicator status-indicator__negative"></span> Ваш аккаунт от API СДЭК не подключён. Убедитесь, что вы указали правильный логин и пароль от API СДЭК.</p>
		<?php endif; ?>
	</td>
</tr>
