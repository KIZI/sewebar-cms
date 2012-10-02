package xquerysearch.grouping.service;

import java.util.List;

import xquerysearch.domain.Group;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;

/**
 * Service providing grouping features.
 * 
 * @author Tomas Marek
 *
 */
public interface GroupingService {

	/**
	 * Divides {@link Result}s from {@link ResultSet} into {@link Group}s.
	 * 
	 * @param resultSet
	 * @param criterion
	 * @return
	 */
	public List<Group> groupBy(ResultSet resultSet, String criterion);
	
}
