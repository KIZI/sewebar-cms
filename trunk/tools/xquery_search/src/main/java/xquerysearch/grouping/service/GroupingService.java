package xquerysearch.grouping.service;

import java.util.List;

import xquerysearch.domain.arbquery.Params;
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
	 * Divides {@link Result}s from {@link List} into {@link Group}s.
	 * 
	 * @param results
	 * @param params
	 * @return
	 */
	public List<Group> groupBy(List<Result> results, Params params);
	
}
