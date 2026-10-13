import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import {PanelBody, Button, TextControl, ToggleControl } from '@wordpress/components';
import metadata from './block.json';

function VideoSelector({ label, url, onSelect, removeVideo, slotIndex }) {
	return (
		<div
			style={{
				marginBottom: '1rem',
			}}
		>
			<p style={{fontWeight: 600, marginBottom: '6px'}}>
				{ label }
			</p>
			<MediaUploadCheck>
				<MediaUpload
					onSelect={( media ) => onSelect( media )}
					allowedTypes={ 'video' }
					render={({ open }) => (
						<>
							{ url ? (
								<>
									<video
										src={ url }
										style={{
											width: '100%',
											marginBottom: '6px',
											borderRadius: '4px',
											maxHeight: '200px'
										}}
										muted
									/>
									<div
										style={{
											display: 'grid',
											gridTemplateColumns: '1fr 1fr',
											gap: '1rem'
										}}
									>
										<Button
											isSecondary
											onClick={ open }
										>
											Replace
										</Button>
										<Button
											isDestructive
											onClick={ () => removeVideo( slotIndex )}
											style={{ border: "1px solid #cc1818" }}
										>
											Remove
										</Button>
									</div>
								</>
							) : (
								<div
									style={{
										display: 'grid',
										gridTemplateColumns: '1fr 1fr',
										gap: '1rem'
									}}
								>
									<Button
										isSecondary
										onClick={ open }
									>
										Select video
									</Button>
									<Button
										isDestructive
										onClick={ () => removeVideo( slotIndex )}
										style={{ border: "1px solid #cc1818" }}
									>
										Remove
									</Button>
								</div>
							)}
						</>
					)}
				/>
			</MediaUploadCheck>
		</div>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();

	const { videoURLs, playAuto, playLoop, playNoSound, playControls, playButton, posterURL } = attributes;

	const updateVideo = ( index, key, value ) => {
		const updated = videoURLs.map( ( item, i ) =>
			i === index ? { ...item, [key]: value } : item,
		);
		setAttributes( { videoURLs: updated } );
	};

	const mergeVideo = ( index, patch ) => {
		console.log(patch);
		const updated = videoURLs.map( ( item, i ) =>
			i === index ? { ...item, ...patch } : item,
		);
		setAttributes( { videoURLs: updated } );
	};

	const addVideo = () => {
		setAttributes({ videoURLs: [ ...videoURLs, { url: '', size: 0 }]});
	};
	const removeVideo = ( index ) => {
		setAttributes({ videoURLs: videoURLs.filter(( _, i ) => i !== index )});
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title="Settings">
					<ToggleControl
						label="Auto play"
						checked={playAuto}
						disabled={playButton}
						onChange={(value) => setAttributes({ playAuto: value, ...(value && { playNoSound: true }) }) }
					/>
					<ToggleControl
						label="Loop"
						checked={playLoop}
						onChange={(value) => setAttributes({ playLoop: value }) }
					/>
					<ToggleControl
						label="Muted"
						checked={playNoSound}
						disabled={playAuto}
						onChange={(value) => setAttributes({ playNoSound: value }) }
					/>
					<ToggleControl
						label="Video Controls"
						checked={playControls}
						disabled={playButton}
						onChange={(value) => setAttributes({ playControls: value }) }
					/>
					<ToggleControl
						label="Centert play button"
						checked={playButton}
						onChange={(value) => setAttributes({ playButton: value, ...(value && { playAuto: false, playControls: false }) }) }
					/>
				</PanelBody>
				<PanelBody title="Video sources">
					<div
						style={{
							maxHeight: '475px',
							overflow: 'auto',
							padding: '0 16px 2px 2px'
						}}
					>
						{ videoURLs.map(( slot, slotIndex ) => (
							<div
								style={
									slotIndex === 0 ?
										null :
										{
											marginTop: '16px',
											paddingTop: '16px',
											borderTop: '1px solid #000'
										}
								}
								 key={ slotIndex }
							>
								<VideoSelector
									label={ `Video ${ slotIndex + 1 }` }
									url={ slot.url }
									onSelect={( media ) => mergeVideo( slotIndex, { url: media.url, width: media.width, height: media.height, mime: media.mime })}
									removeVideo={ removeVideo }
									slotIndex={slotIndex}
								/>
								{ slotIndex !== 0 && (
									<TextControl
										label="Max width breakpoint (px)"
										type="number"
										value={ slot.size || '' }
										onChange={( value ) =>
											updateVideo( slotIndex, 'size', parseInt( value ) || 0 )
										}
									/>
								)}
							</div>
						))}
					</div>
					<Button
						isSecondary
						onClick={ addVideo }
						style={{
							width: '100%',
							marginTop: '16px',
						}}
					>
						+ Add video
					</Button>

					<div style={{ marginTop: '16px', paddingTop: '16px', borderTop: '1px solid #000' }}>
						<p style={{ fontWeight: 600, marginBottom: '6px' }}>Poster image</p>
						<p style={{ fontWeight: 400, fontSize: '12px' }}>Note that this does not change any size just sets itself on the video(s)</p>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={( media ) => setAttributes({ posterURL: media.url })}
								allowedTypes={[ 'image' ]}
								value={ posterURL }
								render={({ open }) => (
									<>
										{ posterURL ? (
											<>
												<img
													src={ posterURL }
													style={{
														width: '100%',
														marginBottom: '6px',
														borderRadius: '4px',
														maxHeight: '200px',
														objectFit: 'cover',
													}}
												/>
												<div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
													<Button isSecondary onClick={ open }>Replace</Button>
													<Button
														isDestructive
														onClick={ () => setAttributes({ posterURL: '' }) }
														style={{ border: '1px solid #cc1818' }}
													>
														Remove
													</Button>
												</div>
											</>
										) : (
											<Button isSecondary onClick={ open }>+ Add poster image</Button>
										)}
									</>
								)}
							/>
						</MediaUploadCheck>
					</div>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				<ServerSideRender
					block={metadata.name}
					skipBlockSupportAttributes
					attributes={attributes}
				/>
			</div>
		</>
	);
}
