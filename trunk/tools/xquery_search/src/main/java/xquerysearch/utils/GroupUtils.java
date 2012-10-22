package xquerysearch.utils;

import java.util.List;

import xquerysearch.domain.grouping.Group;
import xquerysearch.domain.grouping.GroupDescription;

/**
 * Class providing helping methods for using {@link Group}s.
 * 
 * @author Tomas Marek
 * 
 */
public class GroupUtils {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private GroupUtils() {
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param categoryNames
	 * @return
	 */
	public static Group getGroupByCategories(List<Group> groups, List<String> categoryNames, String fieldRef) {
		if (groups == null || categoryNames == null) {
			return null;
		}

		for (Group group : groups) {
			if (isSuitableGroup(group, categoryNames, fieldRef)) {
				return group;
			}
		}

		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param group
	 * @param categoryNames
	 * @return
	 */
	private static boolean isSuitableGroup(Group group, List<String> categoryNames, String fieldRef) {
		if (group == null || categoryNames == null) {
			return false;
		}

		GroupDescription description = group.getDescription();
		if (description == null) {
			return false;
		}
		if (description.getFieldRefName().equals(fieldRef) == false) {
			return false;
		}
		boolean isSuitable = true;
		for (String categoryName : categoryNames) {
			if (description.getCategories().contains(categoryName) == false) {
				isSuitable = false;
			}
		}
		if (isSuitable != false && (description.getCategories().size() != categoryNames.size())) {
			isSuitable = false;
		}

		return isSuitable;
	}
}
