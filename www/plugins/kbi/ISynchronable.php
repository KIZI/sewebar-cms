<?php
/**
 * @version		$Id$
 * @package		KBI
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

/**
 * Interface for document management between CMSs and KBs
 *
 * @package KBI
 */
interface ISynchronable
{
	public function getDocuments();

	/**
	 * Metoda pro zobrazeni dokumentu z XML DB
	 * @param id id dokumentu
	 * @param mgr XmlManager
	 * @return Zobrazeni dokumentu/chyba
	 */
	public function getDocument($id);

	/**
	 * Metoda pro vloĂ„ĹąÄąÄ˝Ă‹ĹĄenĂ„ĹąÄąÄ˝Ă‹ĹĄ dokumentu do XML databĂ„ĹąÄąÄ˝Ă‹ĹĄze
	 *
	 * @param document obsah novĂ„ĹąÄąÄ˝Ă‹ĹĄho dokumentu
	 * @param id nĂ„ĹąÄąÄ˝Ă‹ĹĄzev novĂ„ĹąÄąÄ˝Ă‹ĹĄho dokumentu
	 * @return String output - uloĂ„ĹąÄąÄ˝Ă‹ĹĄeno/neuloĂ„ĹąÄąÄ˝Ă‹ĹĄeno
	 */
	public function addDocument($id, $document, $path = true);

	//public function moreDocuments(String $docs, String $names);

	/**
	 * Metoda pro vymazani z XML db
	 * @param id id dokumentu
	 * @param mgr XmlManager
	 * @return Zprava - splneno/chyba
	 */
	public function deleteDocument($id);

	/**
	 * Metoda pro uloĂ„ĹąÄąÄ˝Ă‹ĹĄenĂ„ĹąÄąÄ˝Ă‹ĹĄ query
	 * @param query obsah query
	 * @param id identifikace query
	 * @return String output - uloĂ„ĹąÄąÄ˝Ă‹ĹĄena/neuloĂ„ĹąÄąÄ˝Ă‹ĹĄena - jiĂ„ĹąÄąÄ˝Ă‹ĹĄ existuje
	 */
	//public function addQuery(String $query, String $id);

	/**
	 * Metoda slouĂ„ĹąÄąÄ˝Ă‹ĹĄĂ„ĹąÄąÄ˝Ă‹ĹĄcĂ„ĹąÄąÄ˝Ă‹ĹĄ k vymazĂ„ĹąÄąÄ˝Ă‹ĹĄnĂ„ĹąÄąÄ˝Ă‹ĹĄ uloĂ„ĹąÄąÄ˝Ă‹ĹĄenĂ„ĹąÄąÄ˝Ă‹ĹĄ query
	 * @param id identifikace query
	 * @return String output - vymazĂ„ĹąÄąÄ˝Ă‹ĹĄna/nenalezena
	 */
	//public function deleteQuery(String $id);

	/**
	 * Metoda pro zĂ„ĹąÄąÄ˝Ă‹ĹĄskĂ„ĹąÄąÄ˝Ă‹ĹĄnĂ„ĹąÄąÄ˝Ă‹ĹĄ uloĂ„ĹąÄąÄ˝Ă‹ĹĄenĂ„ĹąÄąÄ˝Ă‹ĹĄ query
	 * @param id identifikace query
	 * @return String output - obsah query/nenalezena
	 */
	//public function getQuery(String $id);

	/**
	 * Metoda slouĂ„ĹąÄąÄ˝Ă‹ĹĄĂ„ĹąÄąÄ˝Ă‹ĹĄ pro vyhledĂ„ĹąÄąÄ˝Ă‹ĹĄvĂ„ĹąÄąÄ˝Ă‹ĹĄnĂ„ĹąÄąÄ˝Ă‹ĹĄ v XML databĂ„ĹąÄąÄ˝Ă‹ĹĄzi podle zadanĂ„ĹąÄąÄ˝Ă‹ĹĄ podmĂ„ĹąÄąÄ˝Ă‹ĹĄnky
	 * @param id identifikace query, podle kterĂ„ĹąÄąÄ˝Ă‹ĹĄ vyhledĂ„ĹąÄąÄ˝Ă‹ĹĄvat
	 * @param search vstupnĂ„ĹąÄąÄ˝Ă‹ĹĄ podmĂ„ĹąÄąÄ˝Ă‹ĹĄnka pro query
	 */
	//public function query(String $id, String $search, int $typ);
}