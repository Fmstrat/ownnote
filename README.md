# ownNote

## Installation
- Place this app in **owncloud/apps/ownnote** (Rename the extracted ZIP to "ownnote" or you will receive errors)
- Add the following to your **owncloud/config/config.php**:
    - 'custom_csp_policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; frame-src *; img-src *; font-src 'self' data:; media-src *",

## Importing from Evernote or other html notes
The import process will take HTML files on your local hard drive and import the images into the notes as Base64. To import notes:
- Export your notes from Evernote using default settings as individual *html* files
     - If you are importing notes that are not from Evernote, ensure they use the *html* extension, not the *htm* extension. 
- Copy the *html* files and the folders created containing the images into the */Notes* folder in ownCloud
- Load the Notes listing screen. This will import all of the images into the *html* notes, and rename them to *htm*
- Once the rename has taken place, you can safely delete all the image folders that were copied
     - NOTE: Windows Explorer links the *html* file to it folder of images. If you delete the folder, Windows will sometimes delete the newly renamed *htm* file as well. It is recommended you delete the folders from within the Web Interface to be safe. 
