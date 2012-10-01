package xquerysearch.fuzzysearch.service;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;
import xquerysearch.fuzzysearch.evaluator.FuzzySearchEvaluator;

/**
 * Implementation of {@link FuzzySearchService}.
 * 
 * @author Tomas Marek
 * 
 */
public class FuzzySearchServiceImpl implements FuzzySearchService {

	@Autowired
	private FuzzySearchEvaluator evaluator;

	/**
	 * @{inheritDoc
	 */
	@Override
	public ResultSet evaluateResultSet(ResultSet resultSet, ArBuilderQuery query) {
		if (resultSet.getResults() != null) {
			for (Result result : resultSet.getResults()) {
				Double compliance = evaluator.evaluate(result, query);
				result.setQueryCompliance(compliance);
			}
		}
		return resultSet;
	}

}
