package izi_repository.controller;

import izi_repository.domain.Document;
import izi_repository.service.AggregationService;
import izi_repository.service.DocumentService;

import java.util.List;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.commons.lang.NotImplementedException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;


/**
 * Controller for operations with {@link Document}s.
 * 
 * @author Tomas Marek
 * 
 */
@Controller
public class DocumentController extends AbstractController {

	@Autowired
	private DocumentService documentService;

	@Autowired
	private AggregationService aggregationService;

	@RequestMapping(params = "action=getDocsNames", method = RequestMethod.POST)
	public ModelAndView showAllDocuments(HttpServletRequest request, HttpServletResponse response) {
		List<String> names = aggregationService.getAllDocumentsNames();
		StringBuffer namesToResponse = new StringBuffer();
		namesToResponse.append("<result>");
		for (String name : names) {
			namesToResponse.append("<document>" + name + "</document>");
		}
		namesToResponse.append("</result>");
		addResponseContent(namesToResponse.toString(), response);
		return null;
	}

	@RequestMapping(params = "action=getDocument", method = RequestMethod.POST)
	public ModelAndView getDocument(@RequestParam String id, HttpServletRequest request, HttpServletResponse response) {
		if (id.isEmpty()) {
			addResponseContent("<error>Document id cannot be empty!</error>", response);
			return null;
		}
		Document document = documentService.getDocumentById(id);
		if (document != null) {
			addResponseContent(document.getDocBody(), response);
			return null;
		} else {
			addResponseContent("<error>Retrieval of document with id " + id + " failed!</error>", response);
			return null;
		}
	}

	@RequestMapping(params = "action=deleteDocument", method = RequestMethod.POST)
	public ModelAndView deleteDocument(@RequestParam String id, HttpServletRequest request, HttpServletResponse response) {
		if (id.isEmpty()) {
			addResponseContent("<error>Document id cannot be empty!</error>", response);
			return null;
		}
		Boolean wasRemoved = documentService.removeDocument(id);
		String message = "<message>Document with id " + id + " was successfully removed!</message>";
		if (wasRemoved == false) {
			message = "<error>Removal of document with id " + id + " failed!</error>";
		}
		addResponseContent(message, response);
		return null;
	}

	@RequestMapping(params = "action=addDocument", method = RequestMethod.POST)
	public ModelAndView addDocument(@RequestParam String id, @RequestParam String docName, @RequestParam String creationTime, @RequestParam String reportUri, @RequestParam String content, HttpServletRequest request, HttpServletResponse response) {
		if (id.isEmpty()) {
			addResponseContent("<error>Document id cannot be empty!</error>", response);
			return null;
		}
		if (content.isEmpty()) {
			addResponseContent("<error>Document body cannot be empty!</error>", response);
			return null;
		}
		Document docToInsert = new Document(id, content, docName, creationTime, reportUri);
		Boolean wasInserted = documentService.insertDocument(docToInsert);
		String message = "<message>Document with id " + id + " was successfully inserted!</message>";
		if (wasInserted == false) {
			message = "<error>Document insertion failed!</error>";
		}
		addResponseContent(message, response);
		return null;
	}

	@RequestMapping(params = "action=addDocumentMultiple", method = RequestMethod.POST)
	public ModelAndView addMultipleDocuments() {
		throw new NotImplementedException("Not implemented yet...");
	}
}
