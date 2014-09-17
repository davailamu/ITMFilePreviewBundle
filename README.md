ITM File Preview Bundle
========================

desciption

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

1. Add File preview bundle configurations in `config.yml`

    itm_file_preview:
        upload_path: # Upload path in web folder for example uploads
        upload_url:  # Url access to upload folder
        entities:    # Array of entities
            ITMNewsBundle:  # Bundle name
                News:       # Entity name
                    attachment: ~ # Entity field which upload file

2. Add to twig configuration fields templates in `config.yml`

    # Twig Configuration
    twig:
        form:
            resources:
                - 'ITMFilePreviewBundle:Form:fields.html.twig'

3. Add in `AppKernel.php`

    new ITM\FilePreviewBundle\ITMFilePreviewBundle(),

4. Set in Admin Class `itm_file_preview` type

    ->add('attachment', 'itm_file_preview');



Enjoy!

