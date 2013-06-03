package izi_repository.fuzzysearch.service;

import izi_repository.domain.ArQueryInternal;
import izi_repository.domain.ArTsQueryInternal;
import izi_repository.domain.AssociationRuleInternal;
import izi_repository.domain.TaskSettingInternal;
import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.arbquery.tasksetting.ArTsBuilderQuery;
import izi_repository.domain.result.Result;
import izi_repository.domain.result.datadescription.ResultDataDescription;
import izi_repository.fuzzysearch.evaluator.FuzzySearchEvaluator;
import izi_repository.service.DocumentDataDescriptionService;
import izi_repository.service.QueryService;
import izi_repository.sorting.OutputFuzzySorter;
import izi_repository.transformation.ArQueryToInternalTransformer;
import izi_repository.transformation.ArTsQueryToInternalTransformer;
import izi_repository.transformation.AssociationRuleToInternalTransformer;
import izi_repository.transformation.TaskSettingToInternalTransformer;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;


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
		ArQueryInternal aqi = ArQueryToInternalTransformer.transform(query.getArQuery(),
				query.getDataDescription());

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

		return OutputFuzzySorter.sortByCompliance(results);
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
