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

Add File preview bundle configurations in `config.yml`

```
    # app/config/config.yml

    itm_file_preview:
        upload_path: # Upload path in web folder for example uploads
        upload_url:  # Url access to upload folder
        entities:    # Array of entities
            ITMNewsBundle:  # Bundle name
                News:       # Entity name
                    attachment: ~ # Entity field which upload file
```

Add to twig configuration fields templates in `config.yml`

```
    # app/config/config.yml

    twig:
        form:
            resources:
                - 'ITMFilePreviewBundle:Form:fields.html.twig'
```

Add in `AppKernel.php`

```
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new ITM\FilePreviewBundle\ITMFilePreviewBundle(),
        );
    }
```

Set in Admin Class `itm_file_preview` type

```
    <?php
    // src/ITM/NewsBundle/Admin/NewsAdmin.php

    class NewsAdmin extends Admin
    {
        // ...
        protected function configureFormFields(FormMapper $formMapper)
        {
            // ...
            $formMapper->add('attachment', 'itm_file_preview');
            // ...
        }
    }
```

Enjoy!
======
