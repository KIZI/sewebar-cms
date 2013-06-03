package xquerysearch.service;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.oxm.castor.CastorMarshaller;
import org.springframework.stereotype.Service;

import xquerysearch.domain.result.datadescription.ResultDataDescription;
import xquerysearch.transformation.ResultDataDescriptionTransformer;

/**
 * Implementation of {@link DocumentDataDescriptionService}.
 * 
 * @author Tomas Marek
 * 
 */
@Service
public class DocumentDataDescriptionServiceImpl implements DocumentDataDescriptionService {

	@Autowired
	private QueryService queryService;

	@Value("${container.name}")
	private String containerName;

	@Autowired
	@Qualifier("documentDataDescriptionCastor")
	private CastorMarshaller documentDataDescriptionCastor;

	/**
	 * {@inheritDoc}
	 */
	@Override
	public ResultDataDescription getDataDescriptionByDocId(String docId) {
		String result = queryService.queryForSingleValue("collection(\"" + containerName
				+ "\")/PMML[dbxml:metadata(\'dbxml:name\')=\"" + docId + "\"]/DataDescription");
		return ResultDataDescriptionTransformer.transform(documentDataDescriptionCastor, result);
	}

}
