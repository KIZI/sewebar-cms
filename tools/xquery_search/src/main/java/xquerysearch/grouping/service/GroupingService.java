package xquerysearch.grouping.service;

import java.util.List;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.Params;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.result.Result;

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
