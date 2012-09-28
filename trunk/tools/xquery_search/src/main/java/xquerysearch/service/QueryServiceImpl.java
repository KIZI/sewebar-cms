package xquerysearch.service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.oxm.castor.CastorMarshaller;
import org.springframework.stereotype.Service;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.transformer.QueryObjectTransformer;
import xquerysearch.transformer.QueryXpathTransformer;
import xquerysearch.utils.QueryUtils;

/**
 * This service provides querying to database for results.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class QueryServiceImpl extends AbstractService implements QueryService {
	
	@Value("${container.name}")
	protected String containerName;
	
	@Autowired
	private ResultsDao dao;
	
	@Autowired
	@Qualifier("arbQueryCastor")
	private CastorMarshaller arbQueryCastor;
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public ResultSet getResultSet(Query query) {
		ByteArrayOutputStream preparedQuery = QueryUtils.queryPrepare(query.getQueryBody());
		String xpath = QueryUtils.makeXPath(new ByteArrayInputStream(preparedQuery.toByteArray()), false, "sewebar1.dbxml");
        String xquery = "" +
        		"for $ar in subsequence(" + xpath + ", 1, " + 100 + ")"
                + "\n return"
                + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
                    + "\n {$ar/Text}"
                    + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
                + "\n </Hit>" +
            "";
		return dao.getResultSetByXpath(xquery);
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public ResultSet getResultSet(String query) {
		ArBuilderQuery arbQuery = QueryObjectTransformer.transform(arbQueryCastor, query);
		System.out.println("ANTE: " + arbQuery.getArQuery().getAntecedentSetting());
		System.out.println("CONS: " + arbQuery.getArQuery().getConsequentSetting());
		String xpath = QueryXpathTransformer.transformToXpath(arbQuery, containerName);
		xpath = "for $ar in subsequence(" + xpath + ", 1, " + 100 + ")"
                + "\n return"
                + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
                    + "\n {$ar/Text}"
                    + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
                + "\n </Hit>";
		return dao.getResultSetByXpath(xpath);
	}

}
