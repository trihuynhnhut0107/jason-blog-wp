import React from 'react'
import { dispatch, select } from '@wordpress/data'
import { PluginDocumentSettingPanel } from '@wordpress/edit-post'
import { Component } from '@wordpress/element'
import { __ } from '@wordpress/i18n'
import Select2 from 'react-select'
import {
	__experimentalNumberControl as NumberControl,
	Button,
	SelectControl,
	ToggleControl
} from '@wordpress/components'

export default class Sidebar extends Component {

	meta = select('core/editor').getEditedPostAttribute('meta')
	productOptions = []
	restrictionFrequencyOptions = [
		{ value: 'minute', label: __('Minutes') },
		{ value: 'hour', label: __('Hours') },
		{ value: 'day', label: __('Days') },
		{ value: 'week', label: __('Weeks') },
		{ value: 'month', label: __('Months') },
		{ value: 'year', label: __('Years') }
	]

	constructor (props) {

		super(props)

		const _pppDocumentSettingsMeta = {}

		const _pppDocumentProtectedIds = []

		for (let i = 0; i < wpEditorIntegrationObj.productIds.length; i++) {

			const option = {
				label: wpEditorIntegrationObj.productTitles[i],
				value: wpEditorIntegrationObj.productIds[i]
			}

			this.productOptions.push(option)

			if (wpEditorIntegrationObj.selected_product_ids.includes(wpEditorIntegrationObj.productIds[i])) {
				_pppDocumentProtectedIds.push(option)
			}

		}

		let _pppDelayRestrictionFrequency = this.restrictionFrequencyOptions[2].value

		for (let i = 0; i < this.restrictionFrequencyOptions.length; i++) {

			if (this.restrictionFrequencyOptions[i].value === wpEditorIntegrationObj.delay_restriction_frequency) {
				_pppDelayRestrictionFrequency = this.restrictionFrequencyOptions[i].value
				break
			}
		}

		let _pppPageViewRestrictionFrequency = this.restrictionFrequencyOptions[2].value

		for (let i = 0; i < this.restrictionFrequencyOptions.length; i++) {
			if (this.restrictionFrequencyOptions[i].value === wpEditorIntegrationObj.page_view_restriction_frequency) {
				_pppPageViewRestrictionFrequency = this.restrictionFrequencyOptions[i].value
				break
			}
		}

		let _pppExpireRestrictionFrequency = this.restrictionFrequencyOptions[2].value

		for (let i = 0; i < this.restrictionFrequencyOptions.length; i++) {
			if (this.restrictionFrequencyOptions[i].value === wpEditorIntegrationObj.expire_restriction_frequency) {
				_pppExpireRestrictionFrequency = this.restrictionFrequencyOptions[i].value
				break
			}
		}

		let _selectedProtectionType = 'basic-protection'

		if (wpEditorIntegrationObj.delay_restriction_enable) {
			_selectedProtectionType = 'delay-restriction'
		}

		if (wpEditorIntegrationObj.page_view_restriction_enable) {
			_selectedProtectionType = 'page-view-restriction'
		}

		if (wpEditorIntegrationObj.expire_restriction_enable) {
			_selectedProtectionType = 'expiry-restriction'
		}

		_pppDocumentSettingsMeta['product_ids'] = _pppDocumentProtectedIds
		_pppDocumentSettingsMeta['delay_restriction_enable'] = wpEditorIntegrationObj.delay_restriction_enable
		_pppDocumentSettingsMeta['delay_restriction'] = wpEditorIntegrationObj.delay_restriction
		_pppDocumentSettingsMeta['delay_restriction_frequency'] = _pppDelayRestrictionFrequency
		_pppDocumentSettingsMeta['page_view_restriction_enable'] = wpEditorIntegrationObj.page_view_restriction_enable
		_pppDocumentSettingsMeta['page_view_restriction'] = wpEditorIntegrationObj.page_view_restriction
		_pppDocumentSettingsMeta['page_view_restriction_frequency'] = _pppPageViewRestrictionFrequency
		_pppDocumentSettingsMeta['page_view_restriction_enable_time_frame'] = wpEditorIntegrationObj.page_view_restriction_enable_time_frame
		_pppDocumentSettingsMeta['page_view_restriction_time_frame'] = wpEditorIntegrationObj.page_view_restriction_time_frame
		_pppDocumentSettingsMeta['expire_restriction_enable'] = wpEditorIntegrationObj.expire_restriction_enable
		_pppDocumentSettingsMeta['expire_restriction'] = wpEditorIntegrationObj.expire_restriction
		_pppDocumentSettingsMeta['expire_restriction_frequency'] = _pppExpireRestrictionFrequency
		_pppDocumentSettingsMeta['show_warnings'] = wpEditorIntegrationObj.show_warnings

		this.state = {
			pppDocumentSettingsMeta              : _pppDocumentSettingsMeta,
			isOverridePaywallVisible             : false,
			pppDocumentProtectedIds              : _pppDocumentProtectedIds,
			pppDelayRestrictionEnable            : wpEditorIntegrationObj.delay_restriction_enable,
			pppDelayRestriction                  : wpEditorIntegrationObj.delay_restriction,
			pppDelayRestrictionFrequency         : _pppDelayRestrictionFrequency,
			pppPageViewRestrictionEnable         : wpEditorIntegrationObj.page_view_restriction_enable,
			pppPageViewRestriction               : wpEditorIntegrationObj.page_view_restriction,
			pppPageViewRestrictionFrequency      : _pppPageViewRestrictionFrequency,
			pppPageViewRestrictionEnableTimeFrame: wpEditorIntegrationObj.page_view_restriction_enable_time_frame,
			pppPageViewRestrictionTimeFrame      : wpEditorIntegrationObj.page_view_restriction_time_frame,
			pppExpireRestrictionEnable           : wpEditorIntegrationObj.expire_restriction_enable,
			pppExpireRestriction                 : wpEditorIntegrationObj.expire_restriction,
			pppExpireRestrictionFrequency        : _pppExpireRestrictionFrequency,
			pppShowWarnings                      : wpEditorIntegrationObj.show_warnings,
			selectedProtectionType               : _selectedProtectionType,
		}
	}

	render () {
		const allowed_post_type = wpEditorIntegrationObj.post_types.includes(wp.data.select('core/editor').getCurrentPostType());

			const PaywallOverrideControl = wpEditorIntegrationObj.can_use_premium_code ?
				<div className="ppp-control">
					<div className="ppp-control-title">
						{__('Override Restricted Content Message')}
					</div>

					<div className="ppp-control-description">
						{__('By default the Restricted Content Message is pulled from the', 'wc_pay_per_post')} <a
						href={wpEditorIntegrationObj.adminUrl + 'admin.php?page=wc_pay_per_post-settings'}> {__('settings page', 'wc_pay_per_post')}</a>. {__('But you can override that on a product level by adding text below.', 'wc_pay_per_post')}
					</div>

					<Button
						variant="secondary"
						onClick={() => {

							const pppProduct = document.getElementById('ppp-product')

							if (!this.state.isOverridePaywallVisible) {
								pppProduct.style.cssText = 'display: block;'
								document.getElementById('wc-ppp-product-info').scrollIntoView({ behavior: 'smooth' })
							} else {
								pppProduct.style.cssText = 'display: none'
							}

							this.setState({ isOverridePaywallVisible: !this.state.isOverridePaywallVisible })

						}}
					>
						{!this.state.isOverridePaywallVisible ? __('Show Override Message Controls') : __('Hide Override Message Controls')}
					</Button>

				</div>
				: ''

			const DelayRestrictionControl = wpEditorIntegrationObj.can_use_premium_code ?
				<div className="ppp-control-wrapper">
					<div className="ppp-control-title">
						{__('Delay Restriction Enabled')}
					</div>
					<div className="ppp-control-description">
						{__('This allows you to delay the paywall from appearing for a set amount of time.')}
					</div>

					<div className="ppp_interval_control">
						<div className="ppp_interval_control_data">
							<NumberControl
								isShiftStepEnabled={true}
								min={0}
								shiftStep={1}
								value={this.state.pppDelayRestriction}
								onChange={(value) => {
									this.dispatchMeta('pppDelayRestriction', 'delay_restriction', value)
								}}
							/>
						</div>
						<div className="ppp_interval_control_unit">
							<SelectControl
								options={this.restrictionFrequencyOptions}
								value={this.state.pppDelayRestrictionFrequency}
								onChange={(value) => {
									this.dispatchMeta('pppDelayRestrictionFrequency', 'delay_restriction_frequency', value)
								}}
							/>
						</div>
					</div>
					<div className="ppp-control-description">
						{__('Example: For two weeks after publishing this post it is FREE to general public to view.  After two weeks users must purchase specified product to view content of post.')}
					</div>
				</div>
				: ''

			const PageViewRestrictionTimeFrameControl = this.state.pppPageViewRestrictionEnableTimeFrame ?
				<div>
					<NumberControl
						isShiftStepEnabled={true}
						min={0}
						shiftStep={1}
						value={this.state.pppPageViewRestrictionTimeFrame}
						onChange={(value) => {
							this.dispatchMeta('pppPageViewRestrictionTimeFrame', 'page_view_restriction_time_frame', value)
						}}
					/>

					<SelectControl
						options={this.restrictionFrequencyOptions}
						value={this.state.pppPageViewRestrictionFrequency}
						help={__('Over ' + this.state.pppPageViewRestrictionTimeFrame + ' ' + this.state.pppPageViewRestrictionFrequency + (this.state.pppPageViewRestrictionTimeFrame > 1 ? 's' : '') + ' since Last Purchase Date')}
						onChange={(value) => {
							this.dispatchMeta('pppPageViewRestrictionFrequency', 'page_view_restriction_frequency', value)
						}}
					/>
				</div>
				: ''

			const PageViewRestrictionControl = '' !== wpEditorIntegrationObj.can_use_premium_code ?
				<div className="ppp-control-wrapper">
					<div className="ppp-control-title">
						{__('Page View Restriction Enabled')}
					</div>
					<div className="ppp-control-description">
						{__('This allows you to limit the number of page views the user who purchased this product has before the paywall reappears. Options to specify over a set amount of time or forever.')}
					</div>

					<div className="ppp_interval_control">
						<div className="ppp_interval_control_data ppp-control-description">
							<b>{__('Page Views')}</b>
						</div>
						<div className="ppp_interval_control_unit">
							<NumberControl
								isShiftStepEnabled={true}
								min={0}
								shiftStep={1}
								value={this.state.pppPageViewRestriction}
								onChange={(value) => {
									this.dispatchMeta('pppPageViewRestriction', 'page_view_restriction', value)
								}}
							/>
						</div>
					</div>

					<div className="ppp-control-description components-base-control__help">
						{__('Post can be viewed this times before needs to be repurchased.')}
					</div>

					<ToggleControl
						label={__('Enable Views over Time')}
						help={__('Calculate page views over specific amount of time?')}
						checked={this.state.pppPageViewRestrictionEnableTimeFrame}
						onChange={(value) => {
							this.dispatchMeta('pppPageViewRestrictionEnableTimeFrame', 'page_view_restriction_enable_time_frame', value)
						}}
					/>

					{PageViewRestrictionTimeFrameControl}

					<div className="ppp-control-description">
						{__('Example: You only want a customer to be able to view this post no more than two times per week, otherwise they need to repurchase post.')}
					</div>

				</div>
				: ''

			const ExpiryRestrictionControl = '' !== wpEditorIntegrationObj.can_use_premium_code ?
				<div className="ppp-control-wrapper">
					<div className="ppp-control-title">
						{__('Expiry Restriction Enabled')}
					</div>
					<div className="ppp-control-description">
						{__('This allows you to specify an expiration on this post which would require the user to repurchase the product associated with this post.')}
					</div>

					<div className="ppp-control-title">
						<b>{__('Expire after:')}</b>
					</div>

					<div className="ppp_interval_control">
						<div className="ppp_interval_control_data">
							<NumberControl
								isShiftStepEnabled={true}
								min={0}
								shiftStep={1}
								value={this.state.pppExpireRestriction}
								onChange={(value) => {
									this.dispatchMeta('pppExpireRestriction', 'expire_restriction', value)
								}}
							/>
						</div>
						<div className="ppp_interval_control_unit">
							<SelectControl
								options={this.restrictionFrequencyOptions}
								value={this.state.pppExpireRestrictionFrequency}
								onChange={(value) => {
									this.dispatchMeta('pppExpireRestrictionFrequency', 'expire_restriction_frequency', value)
								}}
							/>
						</div>
					</div>
					<div className="ppp-control-description">
						{__('Expire after ' + this.state.pppExpireRestriction + ' ' + this.state.pppExpireRestrictionFrequency + (this.state.pppExpireRestriction > 1 ? 's' : ''))}
					</div>
					<div className="ppp-control-description">
						{__('Example: You have pages or posts that have embedded video and you want to sell a 2 day pass to unlimited viewing of the videos. Once the user purchases the post they have access for 2 days, and at that time they will have to repurchase the product to get access again.')}
					</div>

				</div>
				: ''

			const OptionsControl = '' !== wpEditorIntegrationObj.can_use_premium_code ?
				<div className="ppp-control-wrapper">

					<div className="ppp-control-title">
						{__('Show user page views remaining / time remaining at top of content?')}
					</div>

					<ToggleControl
						label={__('Show it')}
						help={__('Did you want to show to your users how much time is remaining for their purchase or how many page views they have remaining?.')}
						checked={this.state.pppShowWarnings}
						onChange={(value) => {
							this.dispatchMeta('pppShowWarnings', 'show_warnings', value)
						}}
					/>

					<div className="ppp-control-description">
						{__('Templates can be overwritten in your child theme. Look at public/partials/expiration-status.php and public/partials/pageview-status.php')}
					</div>

				</div>
				: ''
if(allowed_post_type){
	return (
		<PluginDocumentSettingPanel
			name="ppp-plugin-sidebar"
			icon="lock"
			title={__('Pay For Post')}
			className="ppp-plugin-sidebar"
		>

			<div className="ppp-control">

				<div className="ppp-control-title">
					{__('Select product(s)')}
				</div>
				<Select2
					isMulti={true}
					label={__('Select product(s)')}
					value={this.state.pppDocumentProtectedIds}
					options={this.productOptions}
					onChange={(value) => {
						this.dispatchMeta('pppDocumentProtectedIds', 'product_ids', value)
					}}
				/>

				<div className="ppp-control-description">
					<a href={wpEditorIntegrationObj.adminUrl + 'post-new.php?post_type=product'}>{__('Create New Product', 'wc_pay_per_post')}</a>
				</div>

			</div>

			<div className="ppp-control">
				<SelectControl
					label={__('Protection type')}
					value={this.state.selectedProtectionType}
					options={[
						{ label: 'Basic protection', value: 'basic-protection' },
						{ label: 'Delay restriction', value: 'delay-restriction' },
						{ label: 'Page view restriction', value: 'page-view-restriction' },
						{ label: 'Expiry restriction', value: 'expiry-restriction' },
					]}
					onChange={(protectionType) => {
						this.setState({
							selectedProtectionType      : protectionType,
							pppDelayRestrictionEnable   : protectionType === 'delay-restriction',
							pppPageViewRestrictionEnable: protectionType === 'page-view-restriction',
							pppExpireRestrictionEnable  : protectionType === 'expiry-restriction',
						})

						this.dispatchMeta('pppDelayRestrictionEnable', 'delay_restriction_enable', protectionType === 'delay-restriction')
						this.dispatchMeta('pppPageViewRestrictionEnable', 'page_view_restriction_enable', protectionType === 'page-view-restriction')
						this.dispatchMeta('pppExpireRestrictionEnable', 'expire_restriction_enable', protectionType === 'expiry-restriction')
					}}
					__nextHasNoMarginBottom
				/>

				<div
					className={this.state.selectedProtectionType === 'delay-restriction' ? '' : 'hidden'}
				>
					<div className="protection-type-option">
						{this.getUpgradeControl()}
						{DelayRestrictionControl}
					</div>
				</div>

				<div
					className={this.state.selectedProtectionType === 'page-view-restriction' ? '' : 'hidden'}
				>
					<div className="protection-type-option">
						{this.getUpgradeControl()}
						{PageViewRestrictionControl}
					</div>
				</div>

				<div
					className={this.state.selectedProtectionType === 'expiry-restriction' ? '' : 'hidden'}
				>
					<div className="protection-type-option">
						{this.getUpgradeControl()}
						{ExpiryRestrictionControl}
					</div>
				</div>

				<div
					className={(this.state.selectedProtectionType === 'expiry-restriction' || this.state.selectedProtectionType === 'page-view-restriction') ? 'ppp-options-container' : 'hidden ppp-options-container'}>
					{OptionsControl}
				</div>
			</div>

			{PaywallOverrideControl}
		</PluginDocumentSettingPanel>
	)
} else {
	return (
		<PluginDocumentSettingPanel>
		</PluginDocumentSettingPanel>
	)
}

	}

	getUpgradeControl () {
		return '' !== wpEditorIntegrationObj.wcppp_freemius_upgrade_url ?
			<div className="ppp-control-description ppp-upgrade">
				<h3>Premium Feature</h3>
				<p>This feature is only available in the Premium version.</p>
				<a className="button button-secondary" href={wpEditorIntegrationObj.wcppp_freemius_upgrade_url} title="upgrade today">Upgrade Now</a>
			</div>
			: ''
	}

	dispatchMeta (stateKey, metaKey, value) {
		const post_meta = this.state.pppDocumentSettingsMeta
		post_meta[metaKey] = value

		this.setState({
			[stateKey]             : value,
			pppDocumentSettingsMeta: post_meta,
		})

		dispatch('core/editor').editPost({
			meta: {
				'_ppp_document_settings_meta': JSON.stringify(this.state.pppDocumentSettingsMeta),
			},
		})
	}
}
