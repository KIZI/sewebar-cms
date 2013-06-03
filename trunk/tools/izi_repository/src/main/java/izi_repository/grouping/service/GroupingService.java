package izi_repository.grouping.service;

import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.Params;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.arbquery.tasksetting.ArTsBuilderQuery;
import izi_repository.domain.grouping.Group;
import izi_repository.domain.result.Result;

import java.util.List;


/**
 * Service providing grouping features.
 * 
 * @author Tomas Marek
 * 
 */
public interface GroupingService {

	/**
	 * Returns {@link List} of {@link Group}s containing {@link Result}s by
	 * {@link ArBuilderQuery}.
	 * 
	 * @param query
	 * @param settings
	 * @return
	 */
	public List<Group> getGroupsByQuery(ArBuilderQuery query, QuerySettings settings);

	/**
	 * Returns {@link List} of {@link Group}s containing {@link Result}s by
	 * {@link ArTsBuilderQuery}.
	 * 
	 * @param query
	 * @param settings
	 * @return
	 */
	public List<Group> getGroupsByQuery(ArTsBuilderQuery query, QuerySettings settings);

	/**
	 * Divides {@link Result}s from {@link List} into {@link Group}s.
	 * 
	 * @param results
	 * @param params
	 * @return
	 */
	public List<Group> groupBy(List<Result> results, Params params);

}
