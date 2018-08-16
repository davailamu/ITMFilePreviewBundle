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
            "url": "https://github.com/archer-developer/ITMFilePreviewBundle.git"
        }
	

Configuration
-------------------------------------

Add File preview bundle configurations in `config.yml`

```
    # app/config/config.yml

    itm_file_preview:
        upload_path: # Upload path in web folder for example uploads
        upload_url:  # Url access to upload folder
        public_dir:  # Default value "web"
        save_old_file: # true/false to save file in storage after uploading a new one
        entities:    # Array of entities
            ITMNewsBundle:  # Bundle name
                News:       # Entity name
                    attachment: ~ # Entity field which upload file
```

Add to twig configuration fields templates in `config.yml`

```
    # app/config/config.yml

    twig:
        form_themes:
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

Add type in form. For example, if you use SonataAdminBundle, set in Admin class `itm_file_preview` type for entity field

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

From Symfony version >=2.8 you can use validation annotation to ensure that file was uploaded
```
    // ...
    use ITM\FilePreviewBundle\Validator\Constraints as ItmAssert;
    
    class News
    {
        // You must use `RequiredFile` assert instead of standard `NotBlank`

        /**
         * @ItmAssert\RequiredFile()
         */
        private $attachment;
    }
```

Enjoy!
======
