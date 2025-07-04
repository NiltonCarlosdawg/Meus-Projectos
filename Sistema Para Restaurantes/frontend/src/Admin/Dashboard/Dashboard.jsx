import ActivityGraph from "./ActivityGraph"
import Grid from "./Grid"
import Topbar from "./Topbar"

const Dashboard = () => {
  return (
    <div className="bg-white rounded-lg pb-4 shadow">
      <Topbar/>
      <Grid/>

    </div>
  )
}

export default Dashboard