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
			if (isSuitableGroupByCategories(group, categoryNames, fieldRef)) {
				return group;
			}
		}

		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param fieldRef
	 * @return
	 */
	public static Group getGroupByFieldRef(List<Group> groups, List<String> fieldRefs) {
		if (groups == null || fieldRefs == null) {
			return null;
		}

		for (Group group : groups) {
			if (isSuitableGroupByFieldRefs(group, fieldRefs)) {
				return group;
			}
		}
		return null;
	}

	/**
	 * TODO documentation
	 * 
	 * @param groups
	 * @param ruleLength
	 * @return
	 */
	public static Group getGroupByRuleLength(List<Group> groups, int ruleLength) {
		if (groups == null) {
			return null;
		}

		for (Group group : groups) {
			if (group.getDescription() != null && group.getDescription().getRuleLength() != null
					&& group.getDescription().getRuleLength() == ruleLength) {
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
	private static boolean isSuitableGroupByCategories(Group group, List<String> categoryNames,
			String fieldRef) {
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

	/**
	 * TODO documentation
	 * 
	 * @param group
	 * @param fieldRefs
	 * @return
	 */
	private static boolean isSuitableGroupByFieldRefs(Group group, List<String> fieldRefs) {
		if (group == null || fieldRefs == null) {
			return false;
		}

		GroupDescription description = group.getDescription();
		if (description == null) {
			return false;
		}

		boolean isSuitable = true;
		for (String fieldRef : fieldRefs) {
			if (description.getFieldRefs().contains(fieldRef) == false) {
				isSuitable = false;
			}
		}
		if (isSuitable == false && (description.getFieldRefs().size() != fieldRefs.size())) {
			return false;
		}
		return isSuitable;
	}
}
