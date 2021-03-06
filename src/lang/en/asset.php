<?php

return [
    'albums'                       => 'Albums',
    'album-create'                 => 'Create album',
    'album-name'                   => 'Enter a name for the album',
    'all'                          => 'All assets',
    'automatic-upload-description' => "File automatically uploaded for use in page\n\nUploaded from: :url",
    'close'                        => 'Close',
    'attributes'                   => 'Attributes',
    'info'                         => 'Info',
    'extension'                    => 'File extension',
    'filename'                     => 'Filename',
    'published-at'                 => 'Published at',
    'history'                      => 'History',
    'history-intro'                => 'These files were previously assigned to this asset but were replaced.',
    'aspect-ratio'                 => 'Aspect ratio',
    'manager'                      => 'Asset Manager',
    'metadata'                     => 'Metadata',
    'none'                         => 'No assets found',
    'title'                        => 'Title',
    'description'                  => 'Description',
    'credits'                      => 'Credits',
    'thumbnail'                    => 'Thumbnail',
    'type-heading'                 => 'Type',
    'filesize'                     => 'Filesize',
    'dimensions'                   => 'Dimensions',
    'uploaded-by'                  => 'Uploaded by',
    'uploaded-on'                  => 'Uploaded on',
    'download'                     => 'Download',
    'downloads'                    => 'Downloads',
    'edited-by'                    => 'Edited by',
    'edited-at'                    => 'Edited at',
    'edit-image'                   => 'Edit image',
    'unsupported'                  => 'File :filename is of an unsuported type: :mimetype',
    'selection'                    => [
        'download' => [
            'about' => [
                'singular' => 'The selected asset will be downloaded as a .zip file',
                'plural'   => 'The :count selected assets will be downloaded as a .zip file',
            ],
            'heading'  => 'Download assets',
            'filename' => 'Enter the name of the download',
            'default'  => 'BoomCMS Asset Download',
        ],
        'delete' => [
            'heading' => 'Delete assets',
            'confirm' => 'Are you sure you want to delete the selected assets?',
        ],
    ],
    'select' => [
        'all'  => 'Select all',
        'none' => 'Select none',
    ],
    'delete' => [
        'heading' => 'Delete asset',
        'confirm' => 'Are you sure you want to delete this asset?',
    ],
    'picker' => [
        'current'  => 'Current Asset',
        'search'   => 'Search Assets',
        'upload'   => 'Upload Asset',
        'selected' => 'Selected assets',
    ],
    'preview' => [
        'heading' => 'Preview',
        'about'   => 'Previews are provided by Google Docs Viewer',
    ],
    'public' => [
        'title' => 'Publicity',
        '0'     => 'Visible to logged in users only',
        '1'     => 'Visible to all users',
    ],
    'replace' => [
        'heading'     => 'Replace asset',
        'drag-drop'   => 'Or drag and drop a file here to upload',
        'select-file' => 'Select a file',
    ],
    'search' => [
        'all-extensions'  => 'All file extensions',
        'heading'         => 'Search assets',
        'results'         => 'Search results',
        'sort'            => 'Sort order',
        'text'            => 'Search name or description',
        'type'            => 'Asset type',
        'extension'       => 'File extension',
        'uploaded-by'     => 'Uploaded by',
        'uploaded-by-all' => 'Uploaded by anyone',
    ],
    'search-shortcuts' => [
        'all'            => 'All assets',
        'all-images'     => 'All images',
        'all-documents'  => 'All documents',
        'all-videos'     => 'All videos',
        'without-albums' => 'Without albums',
    ],
    'sort' => [
        'created_at desc'    => 'Upload time: most recent first',
        'created_at asc'     => 'Upload time: oldest first',
        'last_modified desc' => 'Last modified: most recent first',
        'last_modified asc'  => 'Last modified: oldest first',
        'title asc'          => 'Title A - Z',
        'title desc'         => 'Title Z - A',
        'filesize asc'       => 'Size (smallest)',
        'filesize desc'      => 'Size (largest)',
        'published_at desc'  => 'Publish time: most recent first',
        'published_at asc'   => 'Publish time: oldest first',
        'downloads desc'     => 'Most downloaded',
    ],
    'type' => [
        'all'   => 'All asset types',
        'image' => 'Image',
        'doc'   => 'Document',
        'video' => 'Video',
        'audio' => 'Audio',
    ],
    'upload' => [
        'heading'      => 'Upload',
        'drag-drop'    => 'Or drag and drop files here to upload',
        'errors'       => 'Errors',
        'failed'       => 'Upload failed, please try again later or contact your system administrator if the problem persists',
        'not-writable' => 'Unable to upload assets. Please ensure you asset storage location is writable',
        'select-files' => 'Select files',
    ],
    'loading' => 'Loading thumbnail',
    'failed'  => 'Unable to load thumbnail',
];
