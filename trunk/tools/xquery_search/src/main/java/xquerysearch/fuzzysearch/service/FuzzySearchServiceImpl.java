package xquerysearch.fuzzysearch.service;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.ArTsQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.TaskSettingInternal;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.datadescription.ResultDataDescription;
import xquerysearch.fuzzysearch.evaluator.FuzzySearchEvaluator;
import xquerysearch.service.DocumentDataDescriptionService;
import xquerysearch.service.QueryService;
import xquerysearch.transformation.ArQueryToInternalTransformer;
import xquerysearch.transformation.ArTsQueryToInternalTransformer;
import xquerysearch.transformation.AssociationRuleToInternalTransformer;
import xquerysearch.transformation.TaskSettingToInternalTransformer;

/**
 * Implementation of {@link FuzzySearchService}.
 * 
 * @author Tomas Marek
 * 
 */
public class FuzzySearchServiceImpl implements FuzzySearchService {

	@Autowired
	private FuzzySearchEvaluator evaluator;

	@Autowired
	private QueryService queryService;

	@Autowired
	private DocumentDataDescriptionService descriptionService;

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Result> getFuzzyResultsByQuery(ArBuilderQuery query, QuerySettings settings) {
		if (query == null) {
			return null;
		}
		List<Result> results = queryService.getResultList(query, settings);
		ArQueryInternal aqi = ArQueryToInternalTransformer.transform(query.getArQuery());

		if (results != null) {
			for (Result result : results) {
				if (result != null) {
					ResultDataDescription dataDescription = descriptionService
							.getDataDescriptionByDocId(result.getDocId());
					AssociationRuleInternal ari = AssociationRuleToInternalTransformer.transform(
							result.getRule(), dataDescription);
					double[][] compliance = evaluator.evaluate(ari, aqi);
					result.setQueryCompliance(compliance);
				}
			}
		}

		return results;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public List<Result> getFuzzyResultsByQuery(ArTsBuilderQuery query, QuerySettings settings) {
		if (query == null) {
			return null;
		}

		List<Result> results = queryService.getResultListByTsQuery(query, settings);
		ArTsQueryInternal atqi = ArTsQueryToInternalTransformer.transform(query.getArTsQuery());

		if (results != null) {
			for (Result result : results) {
				if (result != null) {
					TaskSettingInternal tsi = TaskSettingToInternalTransformer.transform(result
							.getTaskSetting());
					double[][] compliance = evaluator.evaluate(tsi, atqi);
					result.setQueryCompliance(compliance);
				}
			}
		}

		return results;
	}
}
