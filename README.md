ITM File Preview Bundle
========================

Bundle for upload files for SonataAdmin

Installing
----------------------------------

First you need to add `itm/file-preview-bundle` to `composer.json`:

    {
		"require": {
        	"itm/file-preview-bundle": "dev-master"
		}
	},
	"repositories": [{
            "type": "vcs",
            "url": "http://gitlab.itmclient.com/symfony-bundles/itmfilepreviewbundle.git"
        }
	

Configuration
-------------------------------------
<ol>
<li>Add File preview bundle configurations in `config.yml`</li>

```
    itm_file_preview:
        upload_path: # Upload path in web folder for example uploads
        upload_url:  # Url access to upload folder
        entities:    # Array of entities
            ITMNewsBundle:  # Bundle name
                News:       # Entity name
                    attachment: ~ # Entity field which upload file
```

<li>Add to twig configuration fields templates in `config.yml`</li>

```
    twig:
        form:
            resources:
                - 'ITMFilePreviewBundle:Form:fields.html.twig'
```

<li>Add in `AppKernel.php`</li>

```
    new ITM\FilePreviewBundle\ITMFilePreviewBundle(),
```

<li>Set in Admin Class `itm_file_preview` type</li>

```
    ->add('attachment', 'itm_file_preview');
```

Enjoy!
======
