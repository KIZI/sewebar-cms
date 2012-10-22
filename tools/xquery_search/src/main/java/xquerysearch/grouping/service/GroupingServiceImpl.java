package xquerysearch.grouping.service;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.arbquery.Params;
import xquerysearch.domain.arbquery.querysettings.GroupingType;
import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.grouping.GroupDescription;
import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Result;
import xquerysearch.utils.GroupUtils;
import xquerysearch.utils.ResultUtils;

/**
 * Implementation of {@link GroupingService}.
 * 
 * @author Tomas Marek
 * 
 */
public class GroupingServiceImpl implements GroupingService {

	/**
	 * @{inheritDoc
	 */
	@Override
	public List<Group> groupBy(List<Result> results, Params params) {
		if (results == null || params == null) {
			return null;
		}

		String groupBy = params.getGroupBy();

		if (groupBy.equals(GroupingType.FIELDREF.getText())) {
			return groupByFieldRef(results, params.getFieldRef());
		}

		return null;
	}

	private List<Group> groupByFieldRef(List<Result> results, String fieldRef) {
		if (results == null || fieldRef == null) {
			return null;
		}

		List<Group> groups = new ArrayList<Group>();

		for (Result result : results) {
			List<BBA> bbas = ResultUtils.getBbasFromResult(result);
			List<String> categoryNames = ResultUtils.getCategoriesFromBbasByFieldRef(bbas, fieldRef);
			Group group = GroupUtils.getGroupByCategories(groups, categoryNames, fieldRef);
			if (group == null) {
				Group newGroup = new Group();
				newGroup.getResults().add(result);
				GroupDescription newDescription = new GroupDescription();
				newDescription.getCategories().addAll(categoryNames);
				newDescription.setFieldRefName(fieldRef);
				newGroup.setDescription(newDescription);

				groups.add(newGroup);
			} else {
				group.getResults().add(result);
			}
		}

		return groups;
	}

}
