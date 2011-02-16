====== KBI Joomla! extensions ======

This package contains Joomla! extension(s) which aims to bridge the gap between
these two types of systems by enabling access to the structured content stored
in a KB from within a CMS. Specifically, it allows the CMS user to query the KB;
the query result is transformed into HTML and included into CMS documents where
it can be easily combined with original CMS content.

The functionality is devided into following extensions.
You need them all installed in Joomla!

===== administrator/com_kbi ======

Brings user interface for managing Sources, Queries and XSLTs. Following MVC
architectural pattern there is a model, controller and view (list and detail
view) for every entity. This component also contains selector and transformator.
Transformator calls KBI library's query with combination of source, query and
XSLT. Selector is a user window for selecting source, query and coresponding
XSLT. Selector window is open from editors-xtd/kbinclude.

===== com_kbi ======

Component contains ARDesiner integrated into Joomla! environment and cooperating
with administrator/com_kbi component.

===== kbi =====

The Knowledge Base Include (KBI) library, which consists of communication
procedures and CMS interfaces. This allows to use one implementation in various
CMSs.

===== editors-xtd/kbinclude =====

Adds KB include button to the Joomla!'s editor. This button opens selector
window from administration compoment (com_kbi).

===== content/kbi =====

Content plugin used by dynamic KBI fragments. The plugin runs before front end
page rendering and it searches for KBI idendificators, executes given query and
replaces original identificator with gained results.