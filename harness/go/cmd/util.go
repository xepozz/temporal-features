package cmd

import "golang.org/x/mod/semver"

// NormalizeSDKVersion adds the "v" prefix semver requires when the version is
// otherwise well formed. Registries report versions without it, while every
// version comparison here expects one. Paths and other non-versions are
// returned untouched.
func NormalizeSDKVersion(sdkVersion string) string {
	if sdkVersion == "" || semver.IsValid(sdkVersion) {
		return sdkVersion
	}
	if prefixed := "v" + sdkVersion; semver.IsValid(prefixed) {
		return prefixed
	}
	return sdkVersion
}

// GoBuildTags collects the set of tags used by different feature files based
// on the given SDK version.
func GoBuildTags(sdkVersion string) (tags []string) {
	sdkVersion = NormalizeSDKVersion(sdkVersion)

	// When realtive paths are used or an otherwise non-semver version is used, we
	// need to assume there are no tags
	if !semver.IsValid(sdkVersion) {
		return nil
	}

	// Add more tags as needed...
	if semver.Compare(sdkVersion, "v1.11.0") < 0 {
		tags = append(tags, "pre1.11.0")
	}
	if semver.Compare(sdkVersion, "v1.12.0") < 0 {
		tags = append(tags, "pre1.12.0")
	}

	return
}
