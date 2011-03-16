there is a bug relating to function _cleanTags in filterinput.php, which fires when a user with author permission edits an article. All tags are stripped on save.  This can be circumvented by changing the code
to
function _cleanTags($source
       {
         return $source;

